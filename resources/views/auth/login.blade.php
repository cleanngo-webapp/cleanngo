{{-- Simple Login --}}

@extends('layouts.auth')

@section('title','Sign In')

@section('content')
	<div class="min-h-screen flex flex-col items-center justify-center">
		<img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Clean N' Go" class="h-20 mb-6" />
		<div class="bg-white/90 rounded-xl shadow p-6 w-full max-w-md">
			<h1 class="text-2xl font-bold text-center">Sign In</h1>
			@if ($errors->any())
				<div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
					{{ $errors->first() }}
				</div>
			@endif
			<form method="POST" action="{{ route('login.post') }}" class="mt-6 space-y-4">
				@csrf
				<div>
					<label class="block text-sm font-medium">Email</label>
					<input type="email" name="email" class="mt-1 w-full border rounded px-3 py-2" placeholder="Enter your email" required />
				</div>
				<div>
					<label class="block text-sm font-medium">Password</label>
					<div class="relative">
						<input id="login_password" type="password" name="password" class="mt-1 w-full border rounded px-3 pr-10 py-2" placeholder="Enter your password" required />
						<button type="button" class="absolute inset-y-0 right-2 my-auto text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility" data-toggle-password data-target="#login_password">
							<i class="ri-eye-line text-xl"></i>
						</button>
					</div>
				</div>
				<div class="flex justify-between items-center">
					<a href="{{ route('register') }}" class="text-emerald-700 cursor-pointer hover:text-brand-highlight">Sign Up</a>
					<button class="bg-brand-green text-white px-4 py-2 rounded cursor-pointer hover:bg-brand-highlight" type="submit">Sign In</button>
				</div>
			</form>
		</div>
		</div>
@endsection


