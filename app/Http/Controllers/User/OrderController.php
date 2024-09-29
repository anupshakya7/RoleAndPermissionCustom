<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        if (isset($request->pid)) {
            $product = Product::find($request->pid);
            $order = new Order();
            $order->product_id = $product->id;
            $order->invoice_no = $product->id.time();
            $order->total = $product->amount;
            $order->save();

            return view('user.order.checkout', compact('product', 'order'));
        }
    }

}
