@extends('layouts.app')

@section('title','Overview')

@section('content')
<div class="max-w-6xl mx-auto pt-20">
    <h1 class="text-3xl font-extrabold text-center">Request an Estimate</h1>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('customer.services') }}" class="p-6 bg-white rounded-xl border hover:bg-emerald-700 hover:text-white shadow">Sofa / Mattress Deep Cleaning</a>
        <a href="{{ route('customer.services') }}" class="p-6 bg-white rounded-xl border hover:bg-emerald-700 hover:text-white shadow">Carpet Deep Cleaning</a>
        <a href="{{ route('customer.services') }}" class="p-6 bg-white rounded-xl border hover:bg-emerald-700 hover:text-white shadow">Home Service Car Interior Detailing</a>
        <a href="{{ route('customer.services') }}" class="p-6 bg-white rounded-xl border hover:bg-emerald-700 hover:text-white shadow">Post Construction Cleaning</a>
        <a href="{{ route('customer.services') }}" class="p-6 bg-white rounded-xl border hover:bg-emerald-700 hover:text-white shadow">Enhanced Disinfection</a>
        <a href="{{ route('customer.services') }}" class="p-6 bg-white rounded-xl border hover:bg-emerald-700 hover:text-white shadow">Glass Cleaning</a>
    </div>
</div>
@endsection


