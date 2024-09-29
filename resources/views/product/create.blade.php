@extends('layout.web')
@section('pagetitle','Create Product')
@section('content')
<div class="my-3">
    <h3 class="d-inline">Add Product</h3>
    <a href="{{route('product.index')}}" class="btn btn-primary float-end">Back</a>
</div>
<form action="{{route('product.store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="product_title" class="form-label">Product Title</label>
        <input type="text" class="form-control" name="product_title" value="{{old('product_title')}}"
            id="product_title">
        @error('product_title')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="product_description" class="form-label">Product Description</label>
        <textarea class="form-control" name="product_description" id="product_description" rows="10"
            cols="80">{{old('product_description')}}</textarea>
        @error('product_description')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="product_image" class="form-label">Product Image</label>
        <input type="file" class="form-control" name="product_image" value="{{old('product_image')}}"
            id="product_image">
        @error('product_image')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label for="product_amount" class="form-label">Product Amount</label>
        <input type="text" class="form-control" name="product_amount" value="{{old('product_amount')}}"
            id="product_amount">
        @error('product_amount')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection