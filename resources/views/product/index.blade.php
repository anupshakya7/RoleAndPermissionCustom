@extends('layout.web')
@section('pagetitle','Product')
@section('content')
<div class="my-3">
    <h3 class="d-inline">Product</h3>
    <a href="{{route('product.create')}}" class="btn btn-primary float-end">Add Product</a>
</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Description</th>
            <th scope="col">Image</th>
            <th scope="col">Amount</th>
            <th scope="col" width="150">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $key=>$product)
        <tr>
            <th scope="row">{{$key}}</th>
            <td>{{$product->title}}</td>
            <td>{{$product->description}}</td>
            <td>{{$product->image}}</td>
            <td>{{$product->is_active}}</td>
            <td>{{$product->amount}}</td>
            <td>
                <a href="" class="btn btn-primary">Edit</a>
                <a href="" class="btn btn-danger">Delete</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">
                <h6>No Found Products</h6>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection