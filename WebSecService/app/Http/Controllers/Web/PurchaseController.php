<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

 
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;


class PurchaseController extends Controller
{
public function purchase(Request $request)
{
    // Check if the user is authenticated and has the 'purchase' permission (Authorization)
    if (!(auth()->check() && auth()->user()->hasPermissionTo(permission: 'purchase'))) abort(401);
    try{
    // Validate the request data
    $request->validate([
        'product_id' => ['required', 'exists:products,id'],
        'quantity' => ['required', 'integer', 'min:1']
    ]);

} catch (ValidationException $e) {
    return redirect()->back()->withInput($request->input())->withErrors($e->validator->errors());
}

    catch (\Exception $e) {
        return redirect()->back()->withInput($request->input())->withErrors(['error' => 'Something went wrong. Please try again.']);       
     }

    // Get the authenticated user credit and product stock
    $user = auth()->user(); // Get the authenticated user
    $product = Product::find($request->product_id);
    $productPrice= $product->price; // Get the product price

    // dd($request->quantity);

    if ($product->discount_percentage > 0 && $request->quantity <= $product->discount_max_products) {
        // Ensure the quantity is not greater than the maximum allowed for discount
        $productPrice = $product->price - ($product->price * ($product->discount_percentage / 100));
    } else {
        // If the discount is not applicable, set the product price to the original price
        $productPrice = $product->price;
    }

    $totalCost = $productPrice* $request->quantity;

    // Validate Stock
    if ($product->stock < $request->quantity) {
       // return back()->with('error', 'Not enough stock available.');

        return redirect()->back()->withInput($request->input())->withErrors(['error' => 'Not enough stock available.']);       

    }

    // Validate Customer Credit
    if ($user->credit < $totalCost) {
       // return back()->with('error', 'Not enough credit.');
        return redirect()->back()->withInput($request->input())->withErrors(['error' => 'Not enough credit.']);       
    }

    // Deduct Stock & Credit
    $product->stock -= $request->quantity;
    $user->credit -= $totalCost;

    
   
    if ($product->discount_percentage > 0 && $request->quantity <= $product->discount_max_products) {
        // Reduce the available discount products by the requested quantity
        $product->discount_max_products -= $request->quantity;
    }
    
    $product->save();
    $user->save();

    // Save purchase record (assuming a Purchase model exists)
        Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' =>  $productPrice,
            'quantity' => $request->quantity,
        ]);

      return redirect()->route('purchased_products')->with('success', 'Product purchased successfully.');

 }

public function  purchasedProducts() {
 
   if (!(auth()->check() && auth()->user()->hasPermissionTo('show_purchased'))) abort(401);
    $user = Auth::user();
    // Get purchases with product details
    $purchases = Purchase::where('user_id', $user->id)
                         ->with('product') // Eager load product details
                         ->get();

    return view('purchases.list', compact('purchases'));
}
}