<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">My Dashboard</h1>
        <form action="{{ route('user.logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                Logout
            </button>
        </form>
    </nav>

    <div class="container mx-auto mt-10 p-4 max-w-4xl">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Welcome, {{ $user->name ?? 'User' }}!</h2>
            <p class="text-gray-600 mb-8">This is your simple dashboard. You can manage your account from here.</p>

            <div class="border-t pt-6">
                <h3 class="text-xl font-semibold text-red-600 mb-4">Danger Zone</h3>
                <p class="text-sm text-gray-500 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                
                <form action="{{ route('user.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Delete My Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
