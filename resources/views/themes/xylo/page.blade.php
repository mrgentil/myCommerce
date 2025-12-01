@extends('themes.xylo.layouts.master')

@section('title', $translation->title ?? 'Page')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-4">{{ $translation->title }}</h1>
            <div class="page-content">
                {!! $translation->content !!}
            </div>
        </div>
    </div>
</div>
@endsection
