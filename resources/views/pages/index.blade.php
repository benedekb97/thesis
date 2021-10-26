@extends('layouts.main')

@section('title', 'Home')

@section('content')
    @isset($user)
        You are logged in<br><a href="{{ route('auth.logout') }}">Log out</a><br>
    @else
        <a href="{{ route('auth.redirect') }}">Log in</a><br>
    @endisset

    This is the content
@endsection
