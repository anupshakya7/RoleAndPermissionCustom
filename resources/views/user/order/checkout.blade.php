@extends('user.layout.web')
@section('content')
<div class="container">
    <h3>Order Details</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="card m-3" style="width: 18rem;">
                <img src="{{asset('storage/'.$product->image)}}" height="250" class="card-img-top p-2"
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
        <div class="col-md-6">
            <h3>Pay With</h3>
            <ul class="list-group">
                <li class="list-group-item">
                    <form method="POST">
                        <input type="text" id="amount" name="amount" value="100" required>
                        <input type="text" id="tax_amount" name="tax_amount" value="10" required>
                        <input type="text" id="total_amount" name="total_amount" value="110" required>
                        <input type="text" id="transaction_uuid" name="transaction_uuid" required>
                        <input type="text" id="product_code" name="product_code" value="EPAYTEST" required>
                        <input type="text" id="product_service_charge" name="product_service_charge" value="0" required>
                        <input type="text" id="product_delivery_charge" name="product_delivery_charge" value="0"
                            required>
                        <input type="text" id="success_url" name="success_url" value="https://esewa.com.np" required>
                        <input type="text" id="failure_url" name="failure_url" value="https://google.com" required>
                        <input type="text" id="signed_field_names" name="signed_field_names"
                            value="total_amount,transaction_uuid,product_code" required>
                        <input type="text" id="signature" name="signature" " required>
                        <input value=" Submit" type="submit">
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection