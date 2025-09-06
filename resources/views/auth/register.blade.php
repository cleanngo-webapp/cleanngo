{{-- Simple Register with Role Selection --}}
@extends('layouts.auth')

@section('title','Sign Up')

@section('content')
<div class="min-h-screen flex items-center justify-center">
	<div class="bg-white/90 rounded-xl shadow p-6 w-full max-w-md">
		<h1 class="text-2xl font-bold text-center">Sign Up</h1>
		@if ($errors->any())
			<div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
				{{ $errors->first() }}
			</div>
		@endif
		<form method="POST" action="{{ route('register.post') }}" class="mt-6 space-y-4">
			@csrf
			<div>
				<label class="block text-sm font-medium">Email</label>
				<input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded px-3 py-2" required />
			</div>
			<div>
				<label class="block text-sm font-medium">Name</label>
				<input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded px-3 py-2" required />
			</div>
			<div>
				<label class="block text-sm font-medium">Role</label>
				<select name="role" class="mt-1 w-full border rounded px-3 py-2" required>
					<option value="customer">Customer</option>
					<option value="cleaner">Cleaner</option>
					<option value="staff">Staff</option>
					<option value="admin">Admin</option>
				</select>
			</div>
			<div>
				<label class="block text-sm font-medium">Password</label>
				<input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2" required />
			</div>
			<div>
				<label class="block text-sm font-medium">Confirm Password</label>
				<input type="password" name="password_confirmation" class="mt-1 w-full border rounded px-3 py-2" required />
			</div>
			<div class="flex justify-between items-center">
				<a href="{{ route('login') }}" class="text-emerald-700">Cancel</a>
				<button class="bg-emerald-700 text-white px-4 py-2 rounded" type="submit">Sign Up</button>
			</div>
		</form>
	</div>
	</div>
@endsection


