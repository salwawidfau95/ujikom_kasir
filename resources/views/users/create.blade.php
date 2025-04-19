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
            <a href="{{ route('users.create') }}" class="text-gray-900 font-semibold">Create User</a>
        </nav>
        <div class="relative">
                <button id="profileMenu" class="rounded-full bg-orange-300 p-2">
                    <i data-lucide="lightbulb" class="w-6 h-6 text-white"></i>
                </button>
                <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white shadow-md rounded-lg p-2 hidden">
                    <a href="" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                    <a href="{{ route('logout') }}" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
                </div>
        </div>
    </div>


    <h1 class="text-2xl font-bold mt-4">User</h1>


    <div class="bg-white p-6 mt-4 rounded-lg shadow-md">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-semibold">Username <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold">Password <span class="text-red-500">*</span></label>
                    <input type="text" id="password" name="password" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold">Email <span class="text-red-500">*</span></label>
                    <input type="text" id="email" name="email" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-semibold">Role <span class="text-red-500">*</span></label>
                    <select id="role" name="role" class="mt-1 w-full border border-gray-300 px-3 py-2 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled selected>Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
            </div>
           
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
