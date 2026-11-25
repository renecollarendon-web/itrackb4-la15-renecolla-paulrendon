@extends('layouts.app')

@section('title', '403 - Not Allowed')

@section('content')
    <div class="text-center mt-5">
        <h1>You are not allowed to do that</h1>
        <p>{{ $exception->getMessage() ?: 'unauthorized. access' }}</p>
        <a href="{{ route('movies.index') }}" class="btn btn-primary">Back to the list</a>
    </div>
@endsection