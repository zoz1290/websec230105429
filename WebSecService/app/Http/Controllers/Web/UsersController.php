<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str; // Add this import

class UsersController extends Controller
{
    ////////////////////////////////////////////////////////////////////////////
    /////User///////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    // Display registration forms (Views)
    public function register(Request $request) {
        return view('users.register');
    }

    // Click register button (Redirect to root page)

    public function doRegister(Request $request) {
        try {
            $request->validate( [
                'name' => ['required', 'string', 'min:3'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
            ]);

            if (User::where('email', $request->email)->exists()) {
                return redirect()->back()->withInput($request->input())->withErrors(['email' => 'Email already exists']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withInput($request->input())->withErrors($e->validator->errors());
        }
        
            catch (\Exception $e) {
                return redirect()->back()->withInput($request->input())->withErrors(['error' => 'Something went wrong. Please try again.']);       
             }
        

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        //Assigment: ● Upon registration, a user is automatically assigned the "Customer" role
        $user->assignRole('Customer'); // Assign "Customer" role

        return redirect('/');
    }

     // Display login forms (Views)
    public function login(Request $request) {
        return view('users.login');
    }
    // Click login button (Redirect to root page)
    public function doLogin(Request $request) {
        $request->validate([  // Fix validation syntax
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/');
        } else {
            return redirect()->back()->withInput($request->input())->withErrors(['email' => 'Invalid credentials']);
        }
    }

    // Display profile page (Views)
    public function profile(Request $request, User $user )
    {
        $user = $user ?? auth()->user();
        if (auth()->id() != $user->id) {
         abort(403, 'You do not have permission to view this user.');
        }

        $permissions = [];
        foreach ($user->permissions as $permission) {
            $permissions[] = $permission;
        }
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission;
            }
        }

        return view('users.profile', compact('user', 'permissions', 'user'));
    }


    // Click logout button (Redirect to login page)
    public function doLogout(Request $request) {
        Auth::logout();
        return redirect()->route('login');
    }
    
    ////////////////////////////////////////////////////////////////////////////
    /////Employee///////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    // Display cusomer list assigned to employee
    public function customers(Request $request )
    {
        if (!(auth()->check() && auth()->user()->hasPermissionTo('show_customers'))) abort(401);

        $user = auth()->user();
// If the user is an Admin, get all customers
if ($user->hasRole('Admin')) {
    $query = User::with('employee')->role('Customer');
}
// If the user is an Employee, get only customers assigned to them
elseif ($user->hasRole('Employee')) {
    $query = User::with('employee')
        ->role('Customer')
        ->where('employee_id', $user->id); // Get only assigned customers
}

// Apply search filter if 'keywords' exist
if ($request->filled('keywords')) {
    $query->where('name', 'like', '%' . $request->keywords . '%');
}

// Execute the query after applying filters
$customers = $query->get();
 return view('customers.list', compact('customers'));
    }
   // Display customer form to add credit
  

    public function editCredit(Request $request, User $user)
    {
        if (!(auth()->check() && auth()->user()->hasPermissionTo('edit_user_credit'))) abort(401);

        // Ensure the authenticated user is the assigned employee for this customer
        if (!(auth()->user()->hasRole('Admin')) && auth()->user()->id !== $user->employee_id) {
            return redirect()->route('customers')->with('error', 'Unauthorized action.');
        }
        return view('customers.edit', compact('user'));
    }
    public function resetCredit(Request $request, User $user)
    {
        if (!(auth()->check() && auth()->user()->hasPermissionTo('reset_credit'))) abort(401);

        // Ensure the authenticated user is the assigned employee for this customer
        if (!(auth()->user()->hasRole('Admin')) && auth()->user()->id !== $user->employee_id) {
            return redirect()->route('customers')->with('error', 'Unauthorized action.');
        }
        return view('customers.edit', compact('user'));
    }
    public function updateresetCredit(Request $request, User $user)
    {
        if (!(auth()->check() && auth()->user()->hasPermissionTo('reset_credit'))) abort(401);
        $request->validate([
            'credit' => 'required|numeric|min:0',
        ]);

        if (!(auth()->user()->hasRole('Admin')) && auth()->user()->id !== $user->employee_id) {
            return redirect()->route('customers')->with('error', 'You are not associated with this customer.');
        }


        $user->credit = 0;
        $user->save();

        return redirect()->route('reset_credit')->with('success', 'Customer credit added successfully.');;
    }


    // Click add credit button (Redirect to customer list page)
    public function updateCredit(Request $request, User $user)
    {
        if (!(auth()->check() && auth()->user()->hasPermissionTo('edit_user_credit'))) abort(401);
        $request->validate([
            'credit' => 'required|numeric|min:0',
        ]);

        if (!(auth()->user()->hasRole('Admin')) && auth()->user()->id !== $user->employee_id) {
            return redirect()->route('customers')->with('error', 'You are not associated with this customer.');
        }


        $user->credit += $request->credit;
        $user->save();

        return redirect()->route('customers')->with('success', 'Customer credit added successfully.');;
    }

    


    ////////////////////////////////////////////////////////////////////////////
    /////Admin///////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    // Display user list
    public function list(Request $request) {
        if (!(auth()->check() && auth()->user()->hasPermissionTo('show_users'))) abort(401);
        // The user is authenticated and has the required permission
        // Start query before pagination
         $query = User::orderBy('name', 'asc')->with('employee');


        // Apply search filter if 'keywords' exist
        if ($request->filled('keywords')) {
            $query->where('name', 'like', '%' . $request->keywords . '%');
        }

        // Paginate after applying filters
        $users = $query->get();  //->paginate(10);
        return view('users.list', compact('users'));

    }
    
   
    // Display user form to create new user (View)
    public function create()
    {
        // Authorization check
        if (!(auth()->check() && auth()->user()->hasPermissionTo('add_users'))) abort(401);

        // To fill the dropdown list    
        $roles = Role::all();
        $permissions = Permission::all();
        $employees = User::role('Employee')->get();

        return view('users.add', compact('roles', 'permissions', 'employees'));
    }

    // Click create user button (Redirect to user list page)
  // Add new user logic 
  public function store(Request $request)
  {
      if (!(auth()->check() && auth()->user()->hasPermissionTo('edit_users'))) abort(401);

      $request->validate([
          'name' => ['required', 'string', 'min:3'],
          'email' => ['required', 'email', 'unique:users'],
          // 'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
          'roles' => ['required', 'array'],
          'roles.*' => ['required', 'in:Admin,Customer,Employee'],
      ]);

      $user = new User();
      $user->name = $request->name;
      $user->email = $request->email;
      $user->credit = $request->credit;
      $user->employee_id = $request->employee_id;
      $user->password = bcrypt($request->password);
      $user->save();

      $user->assignRole($request->roles);

      return redirect()->route('users')->with('success', 'User created successfully.');
  }


// Display user form to edit user (View)
    public function edit(Request $request, User $user) {
       if (!(auth()->check() && auth()->user()->hasPermissionTo('edit_users'))) abort(401);
        // Retrieve all roles
        $roles = Role::all();
        $permissions = Permission::all();
        $employees = User::role('Employee')->get();
       
        return view('users.edit', compact('user', 'roles', 'permissions', 'employees'));

    }

    public function save(Request $request, User $user)
    {
        if (!(auth()->check() && auth()->user()->hasPermissionTo('edit_users'))) abort(401);

        try {
            $request->validate([
                'name' => ['required', 'string', 'min:3'],
                'email' => ['required', 'email', 'unique:users,email,' . $user->id],
               // 'password' => ['nullable', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
                'roles' => ['required', 'array'],
                'roles.*' => ['required', 'in:Admin,Customer,Employee'],
            ]);
        }
        catch (ValidationException $e) {
            return redirect()->back()->withInput($request->input())->withErrors($e->validator->errors());
        } catch (\Exception $e) {
            // The backslash (\) ensures you're referring to the global Exception class.
            return redirect()->back()->withInput($request->input())->withErrors(['error' => 'Something went wrong. Please try again.']);
        }

        $user->fill($request->all());

        $validatedData = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        // Synchronize roles
        if (isset($validatedData['roles'])) {
            $user->syncRoles($validatedData['roles']);
        } else {
            $user->syncRoles([]); // Remove all roles if none are selected
        }

        // Synchronize direct permissions
        if (isset($validatedData['permissions'])) {
            $user->syncPermissions($validatedData['permissions']);
        } else {
            $user->syncPermissions([]); // Remove all direct permissions if none are selected
        }
        $user->save();

        return redirect()->route('users')->with('success', 'User updated successfully.');
    }

    // Delete user
    public function delete(Request $request, User $user)
    {
        if (! (auth()->check() && auth()->user()->hasPermissionTo('delete_users'))) abort(401);

        $user->delete();

        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }

    // Change Password

    // Open Change Password View
    public function editPassword(User $user)
    {
        // Ensure the authenticated user has permission to edit passwords
        if (!(auth()->check() && auth()->user()->can('edit_users'))) {
            abort(403, 'Unauthorized action.');
        }

        return view('users.edit_password', compact('user'));
    }

    // Do Change Password logic then redirect to user Controller
    public function savePassword(Request $request, User $user)
    {
        // Ensure the authenticated user has permission to update passwords
        if (!(auth()->check() && auth()->user()->can('edit_users'))) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the incoming request data
        $request->validate([
            // 'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->symbols()],
        ]);

        // Update the user's password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users')->with('success', 'Password updated successfully.');
    }
    public function alphabits()
    {
        return view('alphabits');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Create new user
                $user = new User();
                $user->name = $googleUser->name;
                $user->email = $googleUser->email;  
                $user->password = bcrypt(Str::random(16)); // Fix str_random to Str::random
                $user->save();
                
                // Assign customer role
                $user->assignRole('Customer');
            }

            Auth::login($user);
            return redirect('/');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Google authentication failed. Please try again.']);
        }
    }
}
// <?php
// namespace App\Http\Controllers\Web;

// use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Validation\Rules\Password;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
// use Illuminate\Support\Facades\Crypt;
// use Illuminate\Support\Facades\Mail;
// use Laravel\Socialite\Facades\Socialite;
// use DB;
// use Artisan;
// use Carbon\Carbon;


// use App\Mail\VerificationEmail;
// use App\Http\Controllers\Controller;
// use App\Models\User;

// class UsersController extends Controller {

// 	use ValidatesRequests;

//     public function list(Request $request) {
//         if(!auth()->user()->hasPermissionTo('show_users'))abort(401);
//         $query = User::select('*');
//         $query->when($request->keywords, 
//         fn($q)=> $q->where("name", "like", "%$request->keywords%"));
//         $users = $query->get();
//         return view('users.list', compact('users'));
//     }

// 	public function register(Request $request) {
//         return view('users.register');
//     }

//     public function doRegister(Request $request) {

//     	try {
//     		$this->validate($request, [
// 	        'name' => ['required', 'string', 'min:5'],
// 	        'email' => ['required', 'email', 'unique:users'],
// 	        'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
// 	    	]);
//     	}
//     	catch(\Exception $e) {

//     		return redirect()->back()->withInput($request->input())->withErrors('Invalid registration information.');
//     	}

    	
//     	$user =  new User();
// 	    $user->name = $request->name;
// 	    $user->email = $request->email;
// 	    $user->password = bcrypt($request->password); //Secure
// 	    $user->save();

//         $title = "Verification Link";
//         $token = Crypt::encryptString(json_encode(['id' => $user->id, 'email' => $user->email]));
//         $link = route("verify", ['token' => $token]);
//         Mail::to($user->email)->send(new VerificationEmail($link, $user->name));
//         return redirect('/');

//     }

//     public function login(Request $request) {
//         return view('users.login');
//     }

//     public function doLogin(Request $request) {
    	
//     	if(!Auth::attempt(['email' => $request->email, 'password' => $request->password]))
//             return redirect()->back()->withInput($request->input())->withErrors('Invalid login information.');

//         $user = User::where('email', $request->email)->first();
//         Auth::setUser($user);

//         if(!$user->email_verified_at)
//             return redirect()->back()->withInput($request->input())->withErrors('Your email is not verified.');


//         return redirect('/');
//     }

//     public function doLogout(Request $request) {
    	
//     	Auth::logout();

//         return redirect('/');
//     }

//     public function profile(Request $request, User $user = null) {

//         $user = $user??auth()->user();
//         if(auth()->id()!=$user->id) {
//             if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
//         }

//         $permissions = [];
//         foreach($user->permissions as $permission) {
//             $permissions[] = $permission;
//         }
//         foreach($user->roles as $role) {
//             foreach($role->permissions as $permission) {
//                 $permissions[] = $permission;
//             }
//         }

//         return view('users.profile', compact('user', 'permissions'));
//     }

//     public function edit(Request $request, User $user = null) {
   
//         $user = $user??auth()->user();
//         if(auth()->id()!=$user?->id) {
//             if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
//         }
    
//         $roles = [];
//         foreach(Role::all() as $role) {
//             $role->taken = ($user->hasRole($role->name));
//             $roles[] = $role;
//         }

//         $permissions = [];
//         $directPermissionsIds = $user->permissions()->pluck('id')->toArray();
//         foreach(Permission::all() as $permission) {
//             $permission->taken = in_array($permission->id, $directPermissionsIds);
//             $permissions[] = $permission;
//         }      

//         return view('users.edit', compact('user', 'roles', 'permissions'));
//     }

//     public function save(Request $request, User $user) {

//         if(auth()->id()!=$user->id) {
//             if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
//         }

//         $user->name = $request->name;
//         $user->save();

//         if(auth()->user()->hasPermissionTo('admin_users')) {

//             $user->syncRoles($request->roles);
//             $user->syncPermissions($request->permissions);

//             Artisan::call('cache:clear');
//         }

//         //$user->syncRoles([1]);
//         //Artisan::call('cache:clear');

//         return redirect(route('profile', ['user'=>$user->id]));
//     }

//     public function delete(Request $request, User $user) {

//         if(!auth()->user()->hasPermissionTo('delete_users')) abort(401);

//         //$user->delete();

//         return redirect()->route('users');
//     }

//     public function editPassword(Request $request, User $user = null) {

//         $user = $user??auth()->user();
//         if(auth()->id()!=$user?->id) {
//             if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
//         }

//         return view('users.edit_password', compact('user'));
//     }

//     public function savePassword(Request $request, User $user) {

//         if(auth()->id()==$user?->id) {
            
//             $this->validate($request, [
//                 'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
//             ]);

//             if(!Auth::attempt(['email' => $user->email, 'password' => $request->old_password])) {
                
//                 Auth::logout();
//                 return redirect('/');
//             }
//         }
//         else if(!auth()->user()->hasPermissionTo('edit_users')) {

//             abort(401);
//         }

//         $user->password = bcrypt($request->password); //Secure
//         $user->save();

//         return redirect(route('profile', ['user'=>$user->id]));
//     }

//     public function verify(Request $request) {
   
//         $decryptedData = json_decode(Crypt::decryptString($request->token), true);
//         $user = User::find($decryptedData['id']);
//         if(!$user) abort(401);
//         $user->email_verified_at = Carbon::now();
//         $user->save();

//         return view('users.verified', compact('user'));
//     }

//     public function redirectToGoogle()
//     {
//         return Socialite::driver('google')->redirect();
//     }

//     public function handleGoogleCallback() {
//         try {
//             $googleUser = Socialite::driver('google')->user();
//             $user = User::updateOrCreate([
//                 'google_id' => $googleUser->id,
//             ], [
//                 'name' => $googleUser->name,
//                 'email' => $googleUser->email,
//                 'google_token' => $googleUser->token,
//                 'google_refresh_token' => $googleUser->refreshToken,
//             ]);
//             Auth::login($user);
//             return redirect('/');
//         } catch (\Exception $e) {
//             return redirect('/login')->with('error', 'Google login failed.'); // Handle errors
//         }
//     }

// } 