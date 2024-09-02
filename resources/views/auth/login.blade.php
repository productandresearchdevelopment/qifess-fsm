@extends('auth.layout')

@section('content')
    <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
        @csrf
        <div class="login__row">
            <svg class="login__icon name svg-icon" viewBox="0 0 20 20">
                <path d="M0,20 a10,8 0 0,1 20,0z M10,0 a4,4 0 0,1 0,8 a4,4 0 0,1 0,-8" />
            </svg>
            <input type="text" name="username" class="login__input name" placeholder="Username" />
        </div>
        <div class="login__row">
            <svg class="login__icon pass svg-icon" viewBox="0 0 20 20">
                <path d="M0,20 20,20 20,8 0,8z M10,13 10,16z M4,8 a6,8 0 0,1 12,0" />
            </svg>
            <input type="password" name="password" class="login__input pass" placeholder="Password" />
        </div>
        <button type="submit" class="login__submit">Sign in</button>
    </form>


    <!-- <p class="login__signup"><a class="btn btn-link" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a></p> -->

@endsection




@section('message')
    @if ($errors->has('username'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('username') }}</strong>
        </span>
    @endif

    @if ($errors->has('password'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
    @endif
@endsection

