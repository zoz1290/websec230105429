<?php


// namespace App\Http\Controllers\Web;

// use App\Http\Controllers\Controller;
// use App\Models\User;
// use Illuminate\Http\Request;

// class EmployeesController extends Controller
// {
//     public function listCustomers()
//     {
//         $customers = User::role('Customer')->get();
//         return view('employees.customers', compact('customers'));
//     }

//     public function addCredit(Request $request, User $user)
//     {
//         $this->validate($request, [
//             'credit' => 'required|numeric|min:0',
//         ]);

//         $user->credit += $request->credit;
//         $user->save();

//         return redirect()->route('customers_list');
//     }
// } 
