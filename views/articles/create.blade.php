@extends('layouts.app')

@section('content')
    <form action="{{ route('articles.store') }}" method="post">
        {{--CSRF保护机制。这样简单的一句话验证也是可以的--}}
        {{ csrf_field() }}
        <label>Title</label>
        <input type="text" name="title" style="width:100%;" value="{{ old('title') }}">
        <label>Content</label>
        <textarea name="content" rows="10" style="width:100%;">{{ old('content') }}</textarea>
        <br/>
        <button type="submit">OK</button>
    </form>
@endsection