@extends('layouts.sidebar')

@section('content')

    <!-- Main Content -->
    <main class="flex-1 p-8 ml-4"> <!-- Tambahkan margin kiri (ml-4) agar lebih dekat ke sidebar -->
        <!-- Header -->
        <div class="flex justify-between items-center">
            <!-- Breadcrumb -->
            <nav class="text-gray-500 text-sm flex items-center space-x-2">
                <a href="" class="flex items-center space-x-1 hover:text-gray-700">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    <span>Home</span>
                </a>
                <span>/</span>
                <span class="text-gray-900 font-semibold">User</span>
            </nav>

            <!-- Profile Button -->
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

        <!-- Success Alert -->
        @if(session('success') && is_array(session('success')))
            @php
                $alert = session('success');
            @endphp

            <div class="p-4 rounded-md mt-4 
                @if($alert['type'] == 'created') bg-green-500 @elseif($alert['type'] == 'deleted') bg-red-500 @endif text-white">
                {{ $alert['message'] }}
            </div>

            <script>
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
            </script>
        @endif

        <!-- User List -->
        <div class="bg-white p-8 shadow-md rounded-lg mt-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold flex items-center space-x-2">
                    <i data-lucide="box"></i> <span>Users</span>
                </h2>
                <a href="{{ route('users.create') }}" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center space-x-2">
                    <i data-lucide="plus-circle"></i> <span>Create User</span>
                </a>
            </div>

            <table class="w-full mt-6 border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-3 text-left">#</th>
                        <th class="border p-3 text-left">Username</th>
                        <th class="border p-3 text-left">Email</th>
                        <th class="border p-3 text-left">Role</th>
                        <th class="border p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr class="border">
                        <td class="border p-3">{{ $loop->iteration }}</td>
                        <td class="border p-3">{{ $user->username }}</td>
                        <td class="border p-3">{{ $user->email }}</td>
                        <td class="border p-3">{{ $user->role }}</td>
                        <td class="border p-3 flex justify-center space-x-3">
                            <!-- Edit Button -->
                            <a href="{{ route('users.up', $user->id) }}" class="text-blue-500 hover:text-blue-700">
                                <i data-lucide="edit"></i>
                            </a>
                            <!-- Delete Button -->
                            <button onclick="confirmDelete({{ $user->id }})" class="text-red-500 hover:text-red-700">
                                <i data-lucide="trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <h3 class="text-lg font-semibold">Confirm Deletion</h3>
            <p class="my-4">Are you sure you want to delete this user?</p>
            <div class="flex justify-end space-x-3">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-400 text-white rounded-md">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Toggle Profile Menu
        document.getElementById('profileMenu').addEventListener('click', function () {
            document.getElementById('profileDropdown').classList.toggle('hidden');
        });

        // Confirm Deletion
        function confirmDelete(userId) {
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');

            // Ubah sesuai dengan route DELETE kamu
            deleteForm.action = "{{ route('users.destroy', $user->id) }}".replace(':id', userId);

            deleteModal.classList.remove('hidden');
            
        }

        // Cancel Deletion
        document.getElementById('cancelDelete').addEventListener('click', function () {
            document.getElementById('deleteModal').classList.add('hidden');
        });
    </script>

@endsection
