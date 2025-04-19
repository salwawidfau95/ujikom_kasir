@extends('layouts.sidebar')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <div class="flex justify-between items-center">
        <nav class="text-gray-500 text-sm flex items-center space-x-2">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-1 hover:text-gray-700">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Home</span>
            </a>
            <span>/</span>
            <a href="{{ route('users.index') }}" class="hover:text-gray-700">User</a>
            <span>/</span>
            <a href="{{ route('users.up', $user->id) }}" class="text-gray-900 font-semibold">Update User</a>
        </nav>
        <div class="relative">
            <button id="profileMenu" class="rounded-full bg-orange-300 p-2">
                <i data-lucide="lightbulb" class="w-6 h-6 text-white"></i>
            </button>
            <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white shadow-md rounded-lg p-2 hidden">
                <a href="" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                <a href="" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </div>

    <h1 class="text-2xl font-bold mt-4">Update User</h1>

    <div class="bg-white p-6 mt-4 rounded-lg shadow-md">
        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-2 gap-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-semibold">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500">
                    @error('username')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold">Email</label>
                    <input type="text" id="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500">
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-semibold">Role</label>
                    <select id="role" name="role" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500">
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    @error('role')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
