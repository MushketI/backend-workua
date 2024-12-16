@extends('layouts.main')
@section('title', 'register')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <h3 class="text-center">Register</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter your name" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">
                                    <span>{{ $message }}</span>
                                </div>
                                @enderror
                            </div>

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
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter your password" value="{{ old('password') }}" required>
                                @error('password')
                                <div class="invalid-feedback">
                                    <span>{{ $message }}</span>
                                </div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="confirm-password" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control @error('password') is-invalid @enderror" id="confirm-password" placeholder="Confirm your password" required>
                                @error('password')
                                <div class="invalid-feedback">
                                    <span>{{ $message }}</span>
                                </div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">{{__('Register')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
