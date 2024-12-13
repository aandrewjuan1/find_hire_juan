<?php
require_once('./Models/User.php');

$user = new User();
$errors = [];
$successMessage = '';

// Redirect authenticated users to the homepage
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['usernameOrEmail'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = $user->login($usernameOrEmail, $password);

    if ($result['success']) {
        header('Location: index.php'); // Redirect to dashboard or homepage
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #bfdbfe, #3b82f6); /* Soft blue gradient */
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">

    <!-- Login Form -->
    <form action="" method="POST" class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-3xl font-bold text-blue-600 mb-6 text-center">Login</h2>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <ul class="mb-4 text-red-600">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Username or Email Input -->
        <div class="mb-4">
            <label for="usernameOrEmail" class="block text-sm font-medium text-blue-600">Username or Email</label>
            <input 
                type="text" 
                name="usernameOrEmail" 
                id="usernameOrEmail" 
                class="w-full border border-blue-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                value="<?= htmlspecialchars($usernameOrEmail ?? '') ?>" 
                required>
        </div>

        <!-- Password Input -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-blue-600">Password</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="w-full border border-blue-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-full w-full hover:bg-blue-600 transition duration-200">Login</button>

        <!-- Registration Link -->
        <p class="mt-4 text-sm text-center text-blue-600">
            Don't have an account? 
            <a href="register.php" class="text-blue-500 underline hover:text-blue-700">Register here</a>.
        </p>
    </form>
</body>
</html>
