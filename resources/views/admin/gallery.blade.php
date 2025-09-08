@extends('layouts.admin')

@section('title','Gallery')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center">Gallery</h1>

    <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @for ($i = 0; $i < 12; $i++)
            <div class="bg-gray-200 aspect-square rounded"></div>
        @endfor
    </div>
</div>
@endsection


