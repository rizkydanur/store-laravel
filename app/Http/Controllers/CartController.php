<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $carts = ProductCart::with(['product.galleries', 'user'])
                                ->where('users_id', Auth::user()->id)
                                ->get();
        return view('pages.cart', [
            'carts' => $carts
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $cart = ProductCart::findOrFail($id);

        $cart->delete();

        return redirect()->route('cart.index');
    }

    public function success () 
    {
        return view('pages.success');
    }
}
