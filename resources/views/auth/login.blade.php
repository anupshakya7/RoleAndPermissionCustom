@extends('layout.web')
@section('pagetitle','Login Page')
@section('content')
<div class="card p-3 mt-4">
    <h3>Login Form</h3>
    @if(Session::has('error'))
    <div class="alert alert-danger" role="alert">
        {{Session::get('error')}}
    </div>
    @endif
    <form action="{{route('auth.login.submit')}}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            @error('email')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password">
            @error('password')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <span class="float-end">Don't have an Account? <a href="{{route('auth.register')}}">Register</a></span>
    </form>
</div>
@endsection