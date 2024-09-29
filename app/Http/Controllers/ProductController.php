<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_title' => 'required|string|max:255',
            'product_description' => 'required|string',
            'product_amount' => 'required|numeric',
            'product_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //Check if Image Exists
        if ($request->file('product_image')) {
            $image = $request->file('product_image');

            $filename = Str::slug($request->product_title).'-'.time().'.'.$image->getClientOriginalExtension();

            //Store the image with the new Filename
            $imagePath = $image->storeAs('images/products', $filename, 'public');
        }

        $product = Product::create([
            'title' => $request->product_title,
            'description' => $request->product_description,
            'image' => $imagePath,
            'is_active' => true,
            'amount' => $request->product_amount,
        ]);

        if ($product) {
            return redirect()->route('product.index')->with('success', 'Product Inserted Successfully!!!');
        } else {
            return redirect()->back()->with('error', 'Failed to Inserted Product!!!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'product_title' => 'required|string|max:255',
            'product_description' => 'required|string',
            'product_status' => 'required|numeric',
            'product_amount' => 'required|numeric',
            'product_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('product_image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $image = $request->file('product_image');

            $filename = Str::slug($request->product_title).'-'.time().'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images/products', $filename, 'public');
        }


        $updateItem = $product->update([
            'title' => $request->product_title,
            'description' => $request->product_description,
            'image' => $imagePath,
            'is_active' => $request->product_status,
            'amount' => $request->product_amount,
        ]);

        if ($updateItem) {
            return redirect()->route('product.index')->with('success', 'Product Inserted Successfully!!!');
        } else {
            return redirect()->back()->with('error', 'Fail to Update Product');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $deleteItem = $product->delete();
        if ($deleteItem) {
            return redirect()->back()->with('success', 'Product Delete Successfully!!!');
        } else {
            return redirect()->back()->with('error', 'Fail to Delete Product');
        }
    }
}
