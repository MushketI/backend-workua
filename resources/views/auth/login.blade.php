@extends('layouts.main')
@section('title', 'login')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter your email" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">
                                    <span>{{ $message }}</span>
                                </div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control @error('email') is-invalid @enderror" id="password" placeholder="Enter your password" value="{{ old('password') }}" required>
                                @error('password')
                                <div class="invalid-feedback">
                                    <span>{{ $message }}</span>
                                </div>
                                @enderror
                            </div>

                            <div class="form-check">
                                <input type="checkbox" name="remember_me" class="form-check-input" id="remember_token" {{ old('remember_me') ? 'checked' : '' }}>
                                <label class="form-check-label" for="rememberMe">
                                    Запомнить меня
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>

                            @if(Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">

                                    Forgot Your Password?
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

