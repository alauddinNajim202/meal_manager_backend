<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Email Verified</title>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    
    <div class="text-center">
        <div class="text-green-500 text-6xl mb-4">✓</div>
        <h1 class="text-2xl font-bold text-gray-900">Email Verified!</h1>
        <p class="text-gray-500 mt-2">Opening app...</p>
        <div class="mt-6">
            <a href="anandkashi://login" class="text-blue-600 hover:underline">Click here if the app doesn't open automatically</a>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.location.href = "anandkashi://login";
            }, 500);
        };
    </script>
</body>
</html>
