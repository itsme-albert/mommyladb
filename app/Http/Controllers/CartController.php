<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carts = Cart::all();
        return $carts;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::table('carts')->insert([
            'user_id'=>Auth::user()->id,
            'product_id'=>$request->product_id,
            'quantity'=>$request->quantity,
            'modification'=>$request->modification
        ]);

        return $request;
    }

    /**
     * Display the specified resource.
     */
    public function showByUser($user_id)
{
    // Retrieve the cart items for the specific user with the related product information
    $carts = Cart::where('user_id', $user_id)->with('product')->get();

    if ($carts->isEmpty()) {
        return response()->json(['message' => 'No items found in cart'], 404);
    }

    return response()->json($carts, 200);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {

        DB::table('carts')
        ->where('id', $cart->id)  
        ->update([
            'quantity' => $request->input('quantity'), 
            'modification' => $request->input('modification'), 
            'updated_at' => now(), 
        ]);

        return $request;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return response()->json(['message' => 'Item removed from cart successfully']);
    }
}
