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
            <th scope="col">Status</th>
            <th scope="col">Amount</th>
            <th scope="col" width="150">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $key=>$product)
        <tr>
            <th scope="row">{{$key+1}}</th>
            <td>{{$product->title}}</td>
            <td>{!!$product->description!!}</td>
            <td><img src="{{asset('/storage/'.$product->image)}}" alt="{{$product->title}}" width="100" height="100">
            </td>
            <td><span
                    class="badge {{$product->is_active == 1 ? 'text-bg-success':'text-bg-warning'}}">{{$product->is_active
                    == 1 ? 'Active':'Inactive'}}</span></td>
            <td>Rs.{{$product->amount}}</td>
            <td>
                <a href="{{route('product.edit',$product)}}" class="btn btn-primary d-inline">Edit</a>
                <form action="{{route('product.destroy',$product)}}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger d-inline">Delete</button>
                </form>
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