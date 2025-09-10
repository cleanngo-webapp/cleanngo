{{-- Simple Register with Role Selection --}}
@extends('layouts.auth')

@section('title','Sign Up')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center">
	<img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Clean N' Go" class="h-20 mb-6" />
	<div class="bg-white/90 rounded-xl shadow p-6 w-full max-w-md">
		<h1 class="text-2xl font-bold text-center">Sign Up</h1>
		@if ($errors->any())
			<div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
				{{ $errors->first() }}
			</div>
		@endif
		<form method="POST" action="{{ route('register.post') }}" class="mt-6 space-y-4">
			@csrf
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
					<label class="block text-sm font-medium">First Name</label>
					<input type="text" name="first_name" value="{{ old('first_name') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="Enter your first name" required />
					@error('first_name')
						<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>
				<div>
					<label class="block text-sm font-medium">Last Name</label>
					<input type="text" name="last_name" value="{{ old('last_name') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="Enter your last name" required />
					@error('last_name')
						<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>
			</div>
			<div>
				<label class="block text-sm font-medium">Username</label>
				<input type="text" name="username" value="{{ old('username') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="Choose a username" required />
				@error('username')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>
			<div>
				<label class="block text-sm font-medium">Email</label>
				<input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="Enter your email" required />
			</div>
			<div>
				<label class="block text-sm font-medium">Contact</label>
				<input type="text" name="contact" value="{{ old('contact') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="Enter your contact number" />
			</div>
			<div>
				<label class="block text-sm font-medium">Role</label>
				<select name="role" class="mt-1 w-full border rounded px-3 py-2 cursor-pointer" required>
					<option value="customer">Customer</option>
					<option value="employee">Employee</option>
				</select>
			</div>
			<div>
				<label class="block text-sm font-medium">Password</label>
				<div class="relative">
					<input id="register_password" type="password" name="password" class="mt-1 w-full border rounded px-3 pr-10 py-2" placeholder="Enter your password" required />
					<button type="button" class="absolute inset-y-0 right-2 my-auto text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility" data-toggle-password data-target="#register_password">
						<i class="ri-eye-line text-xl cursor-pointer"></i>
					</button>
				</div>
			</div>
			<div>
				<label class="block text-sm font-medium">Confirm Password</label>
				<div class="relative">
					<input id="register_password_confirmation" type="password" name="password_confirmation" class="mt-1 w-full border rounded px-3 pr-10 py-2" placeholder="Confirm your password" required />
					<button type="button" class="absolute inset-y-0 right-2 my-auto text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility" data-toggle-password data-target="#register_password_confirmation">
						<i class="ri-eye-line text-xl cursor-pointer"></i>
					</button>
				</div>
			</div>
			<div class="flex justify-between items-center">
				<a href="{{ route('login') }}" class="text-emerald-700 cursor-pointer hover:text-brand-highlight">Cancel</a>
				<button class="bg-emerald-700 text-white px-4 py-2 rounded cursor-pointer hover:bg-brand-highlight" type="submit">Sign Up</button>
			</div>
		</form>
	</div>
	</div>
@endsection


