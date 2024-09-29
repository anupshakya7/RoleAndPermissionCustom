@extends('layout.web')
@section('pagetitle','Edit Product')
@section('content')
<div class="my-3">
    <h3 class="d-inline">Edit Product</h3>
    <a href="{{route('product.index')}}" class="btn btn-primary float-end">Back</a>
</div>
<form action="{{route('product.update',$product)}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="product_title" class="form-label">Product Title</label>
        <input type="text" class="form-control" name="product_title" value="{{old('product_title',$product->title)}}"
            id="product_title">
        @error('product_title')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="product_description" class="form-label">Product Description</label>
        <textarea class="form-control" name="product_description" id="product_description" rows="10"
            cols="80">{{old('product_description',$product->description)}}</textarea>
        @error('product_description')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="product_image" class="form-label">Product Image</label>
        <input type="file" class="form-control" name="product_image" value="{{old('product_image',$product->image)}}"
            id="product_image">
        @error('product_image')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="product_status">Status</label>
        <select class="form-select" name="product_status" id="product_status">
            <option selected>Choose Status</option>
            <option value="1" {{$product->is_active==1 ? "selected":""}}>Active</option>
            <option value="0" {{$product->is_active==0 ? "selected":""}}>Inactive</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="product_amount" class="form-label">Product Amount</label>
        <input type="text" class="form-control" name="product_amount" value="{{old('product_amount',$product->amount)}}"
            id="product_amount">
        @error('product_amount')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection