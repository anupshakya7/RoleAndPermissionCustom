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
                {{-- <li class="list-group-item bg-dark p-3">
                    <form action="https://uat.esewa.com.np/epay/main" method="POST">
                        <input type="hidden" name="tAmt" value="{{$product->amount}}">
                        <input type="hidden" name="amt" value="{{$product->amount}}">
                        <input type="hidden" name="txAmt" value="0">
                        <input type="hidden" name="psc" value="0">
                        <input type="hidden" name="psc" value="0">
                        <input type="hidden" name="pdc" value="0">
                        <input type="hidden" name="scd" value="epay_amount">
                        <input type="hidden" name="pid" value="{{$order->invoice_no}}">
                        <input type="hidden" name="su" value="{{route('esewa.success')}}">
                        <input type="hidden" name="fu" value="{{route('esewa.fail')}}">
                        <input type="image" src="{{asset('images/esewa.png')}}" width="150" height="40" alt="Submit">
                    </form>
                </li> --}}
                <li class="list-group-item bg-dark p-3">
                    <input type="image" src="{{asset('images/fonepay.png')}}" width="150" height="40" alt="Submit">
                    <form action="" method="GET" id="payment_form">
                        <input type="hidden" name="PID" value="{{$PID}}">
                        <input type="hidden" name="MD" value="{{$MD}}">
                        <input type="hidden" name="AMT" value="{{$AMT}}">
                        <input type="hidden" name="CRN" value="{{$CRN}}">
                        <input type="hidden" name="DT" value="{{$DT}}">
                        <input type="hidden" name="R1" value="{{$R1}}">
                        <input type="hidden" name="R2" value="{{$R2}}">
                        <input type="hidden" name="DV" value="{{$DV}}">
                        <input type="hidden" name="RU" value="{{$RU}}">
                        <input type="hidden" name="PRN" value="{{$PRN}}">
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection