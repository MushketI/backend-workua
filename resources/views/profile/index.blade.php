@extends('layouts.main')
@section('title', 'login')


@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header  text-black">
                        <h4>Профиль пользователя</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            <img src="https://via.placeholder.com/150" alt="image">
{{--                            <img src="{{ asset('images/default-profile.png') }}" alt="Аватар пользователя" class="rounded-circle" width="150" height="150">--}}
                        </div>

                        @if (session('status_update'))
                            <div class="alert alert-{{ session('status_code') }}">
                                {{ session('status_update') }}
                            </div>
                        @endif

                        <form action="{{ route('user-profile-information.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Имя</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}">
                                @error('name')
                                <div class="error">
                                    <p class="text-danger">{{ $message }}</p>
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Электронная почта</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')
                                <div class="error">
                                    <p class="text-danger">{{ $message }}</p>
                                </div>
                                @enderror
                            </div>

{{--                            <div class="mb-3">--}}
{{--                                <label for="password" class="form-label">Новый пароль</label>--}}
{{--                                <input type="password" class="form-control" id="password" name="password">--}}
{{--                            </div>--}}

{{--                            <div class="mb-3">--}}
{{--                                <label for="password_confirmation" class="form-label">Подтвердите пароль</label>--}}
{{--                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">--}}
{{--                            </div>--}}

                            <button type="submit" class="btn btn-primary">Обновить профиль</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



