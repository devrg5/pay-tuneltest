@extends('master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1>Bienvenido a Pay Bolivia</h1>
            </div>
        </div>
    </div>
@endsection

{{-- @if (Route::has('login'))
    <div class="flex-center position-ref full-height">
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    </div>
@endif --}}
