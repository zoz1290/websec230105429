<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;

class ProductsController extends Controller {

    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth:web')->except('list');
    }

    public function list(Request $request) {

        // check if role is customer get only unhold products

      //  dd(auth()->user()); 

      $query = Product::select("products.*");

      
        

        $query->when($request->keywords, 
        fn($q)=> $q->where("name", "like", "%$request->keywords%"));

        $query->when($request->min_price, 
        fn($q)=> $q->where("price", ">=", $request->min_price));
        
        $query->when($request->max_price, fn($q)=> 
        $q->where("price", "<=", $request->max_price));
        
        $query->when($request->order_by, 
        fn($q)=> $q->orderBy($request->order_by, $request->order_direction??"ASC"));
        
        $products = $query->get();

        return view('products.list', compact('products'));
    }

    public function edit(Request $request, ?Product $product = null) {

        if(!auth()->user()) return redirect('/');
        if(!auth()->user()->hasPermissionTo('edit_products')) abort(401);

        // Open  prodect edit page or add new product page
        $product = $product??new Product();

        return view('products.edit', compact('product'));
    }

    public function save(Request $request, ?Product $product = null) {
        if(!auth()->user()->hasPermissionTo('edit_products')) abort(401);
        $this->validate($request, [
            'code' => ['required', 'string', 'max:32'],
            'name' => ['required', 'string', 'max:128'],
            'model' => ['required', 'string', 'max:256'],
            'description' => ['required', 'string', 'max:1024'],
            'price' => ['required', 'numeric'],
        ]);

        // Add or update product
        $product = $product??new Product();
        $product->fill($request->all());
        $product->save();

        return redirect()->route('products_list')->with('success', 'Product saved successfully.');
    }

    public function delete(Request $request, Product $product) {

        if(!auth()->user()->hasPermissionTo('delete_products')) abort(401);

        $product->delete();

        return redirect()->route('products_list')->with('success', 'Product deleted successfully.');
    }

    // public function purchase(Request $request, Product $product) {
    //     $user = auth()->user();

    //     if (!$user) {
    //         return redirect()->route('login');
    //     }

    //     if ($user->credit < $product->price) {
    //         return redirect()->back()->withErrors('Insufficient credit.');
    //     }

    //     if ($product->quantity <= 0) {
    //         return redirect()->back()->withErrors('Product out of stock.');
    //     }

    //     $user->credit -= $product->price;
    //     $user->save();

    //     $product->quantity -= 1;
    //     $product->save();

    //     // Save purchase record (assuming a Purchase model exists)
    //     Purchase::create([
    //         'user_id' => $user->id,
    //         'product_id' => $product->id,
    //     ]);

    //     return redirect()->route('products_list')->with('success', 'Product purchased successfully.');
    // }

    // public function listPurchasedProducts() {
    //     $user = auth()->user();
    //     $purchases = Purchase::where('user_id', $user->id)->get();

    //     return view('products.purchased', compact('purchases'));
    // }

    public function update(Request $request, Product $product)
    {  if(!auth()->user()->hasPermissionTo('edit_products')) abort(401);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $product->name = $request->name;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

   
    public function discount(Request $request, ?Product $product = null) {
        if(!auth()->user()->hasPermissionTo('manage_discounts')) abort(401);

        $this->validate($request, [
            'discount' => ['required', 'numeric', 'between:0,100'],
            'max_count' => ['required', 'numeric', 'between:0,100'],
        ]);

        $product->discount_percentage = $request->discount;
        $product->discount_max_products = $request->max_count;
        $product->save();

        return redirect()->route('products_list')->with('success', 'Product discounted successfully.');
    }
}