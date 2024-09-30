@extends('user.layout.web')
@section('content')
<div id="carouselExample" class="carousel slide">
    <div class="carousel-inner" style="height:500px;">
        <div class="carousel-item active">
            <img src="{{asset('banner/1.jpg')}}" class="d-block w-100" alt="Banner 1">
        </div>
        <div class="carousel-item">
            <img src="{{asset('banner/2.jpg')}}" class="d-block w-100" alt="Banner 2">
        </div>
        <div class="carousel-item">
            <img src="{{asset('banner/3.jpg')}}" class="d-block w-100" alt="Banner 3">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<!-- Product Section-->
<section class="my-5">
    <div class="container">
        <h3 class="my-4">Latest Product</h3>
        <div class="product d-inline-flex">
            @forelse($latestProducts as $latestProduct)
            <div class="card m-3" style="width: 18rem;">
                <img src="{{asset('storage/'.$latestProduct->image)}}" height="280" class="card-img-top p-2"
                    alt="{{$latestProduct->title}}">
                <div class="card-body">
                    <h5 class="card-title">{{$latestProduct->title}}</h5>
                    <p class="card-text">{!! Illuminate\Support\Str::limit($latestProduct->description,200,'...')!!}</p>
                    <p class="card-text text-danger"><b>Rs.{{$latestProduct->amount}}</b></p>
                    <form action="{{route('checkout')}}" method="POST">
                        @csrf
                        <input type="hidden" name="pid" value="{{$latestProduct->id}}">
                        <button type="submit" class="btn btn-primary float-end">Buy Now</button>
                    </form>
                </div>
            </div>
            @empty
            <h4>No Latest Product</h4>
            @endforelse
        </div>
    </div>
</section>
<!-- Product Section-->
@endsection