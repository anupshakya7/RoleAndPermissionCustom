@extends('user.order.checkout')
@section('content')
<h3>Order Details</h3>
<div class="row">
    <div class="col-md-4">
        <div class="card m-3" style="width: 18rem;">
            <img src="{{asset('storage/'.$product->image)}}" height="250" class="card-img-top"
                alt="{{$product->title}}">
            <div class="card-body">
                <h5 class="card-title">{{$product->title}}</h5>
                <p class="card-text">{!! Illuminate\Support\Str::limit($product->description,200,'...')!!}</p>
                <p class="card-text text-danger"><b>Rs.{{$product->amount}}</b></p>
                <form action="{{route('checkout')}}" method="POST">
                    @csrf
                    <input type="hidden" name="pid" value="{{$product->id}}">
                    <button type="submit" class="btn btn-primary float-end">Buy Now</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection