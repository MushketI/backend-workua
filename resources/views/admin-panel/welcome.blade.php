@extends('layouts.main')
@section('title', 'login')

@section('content')
    <div class="container">

        @guest
        <h1 class="text-center mt-5" >Welcome to the admin panel</h1>
            <p class="text-center text-muted fs-5">Please log in to your account to continue working.</p>
            <div class="container-md d-flex justify-content-md-center mt-5">
                <a href="/login" class="btn btn-primary w-25">Login</a>
            </div>
        @endguest

    </div>
@endsection

