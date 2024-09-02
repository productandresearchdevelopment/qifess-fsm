@extends('auth.layout')

@section('content')
    <form method="POST" action="{{ route('password.email') }}" aria-label="{{ __('Reset Password') }}">
        @csrf
        <div class="login__row">
            <svg class="login__icon name svg-icon" viewBox="0 0 20 20">
                <path d="M0,20 a10,8 0 0,1 20,0z M10,0 a4,4 0 0,1 0,8 a4,4 0 0,1 0,-8" />
            </svg>
            <input type="text" name="email" class="login__input name" placeholder="Email" value="{{ old('email') }}" required />
        </div>
        
        <button type="submit" class="login__submit">{{ __('Send Password Reset Link') }}</button>
    </form>

    <p class="login__signup"><a class="btn btn-link" href="{{ route('login') }}">Back To Login Page</a></p>
    
@endsection


@section('message')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->has('email'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
    @endif
@endsection