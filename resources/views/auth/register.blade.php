@extends('layout.web')
@section('pagetitle','Login Page')
@section('content')
<div class="card p-3 mt-4">
    <h3>Register Form</h3>
    @if(Session::has('error'))
    <div class="alert alert-danger" role="alert">
        {{Session::get('error')}}
    </div>
    @elseif(Session::has('success'))
    <div class="alert alert-success" role="alert">
        {{Session::get('success')}}
    </div>
    @endif
    <form action="{{route('auth.register.submit')}}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Fullname</label>
            <input type="text" class="form-control" id="fullname" value="{{old('fullname')}}" name="fullname">
            @error('fullname')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" value="{{old('email')}}" name="email"
                aria-describedby="emailHelp">
            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            @error('email')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" value="{{old('password')}}" name="password">
            @error('password')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                value="{{old('password_confirmation')}}">
            @error('password_confirmation')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <span class="float-end">Already have an Account? <a href="{{route('auth.login')}}">Login</a></span>
    </form>
</div>
@endsection