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
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $repeatPassword = $_POST['repeatPassword'] ?? '';
    $roleID = $_POST['roleID'] ?? '';

    // Pass repeat password and roleID for validation and registration
    $result = $user->register($username, $email, $password, $repeatPassword, $roleID);

    if ($result['success']) {
        $successMessage = $result['message'];
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
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #bfdbfe, #3b82f6); /* Soft blue gradient */
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen">

    <!-- Registration Form -->
    <form action="" method="POST" class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-3xl font-bold text-blue-600 mb-6 text-center">Register</h2>

        <!-- Success Message -->
        <?php if (!empty($successMessage)): ?>
            <p class="text-green-600 mb-4"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <ul class="mb-4 text-red-600">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Username Input -->
        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-blue-600">Username</label>
            <input 
                type="text" 
                name="username" 
                id="username" 
                class="w-full border border-blue-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                value="<?= htmlspecialchars($username ?? '') ?>" 
                required>
        </div>

        <!-- Email Input -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-blue-600">Email</label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                class="w-full border border-blue-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                value="<?= htmlspecialchars($email ?? '') ?>" 
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

        <!-- Repeat Password Input -->
        <div class="mb-4">
            <label for="repeatPassword" class="block text-sm font-medium text-blue-600">Repeat Password</label>
            <input 
                type="password" 
                name="repeatPassword" 
                id="repeatPassword" 
                class="w-full border border-blue-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                required>
        </div>

        <!-- Role Selection -->
        <div class="mb-4">
            <label for="roleID" class="block text-sm font-medium text-blue-600">Register As</label>
            <select 
                name="roleID" 
                id="roleID" 
                class="w-full border border-blue-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                required>
                <option value="">-- Select Role --</option>
                <option value="1" <?= (isset($roleID) && $roleID == 1) ? 'selected' : '' ?>>Applicant</option>
                <option value="2" <?= (isset($roleID) && $roleID == 2) ? 'selected' : '' ?>>HR</option>
            </select>
        </div>

        <!-- Register Button -->
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-full w-full hover:bg-blue-600 transition duration-200">Register</button>

        <!-- Login Link -->
        <p class="mt-4 text-sm text-center text-blue-600">
            Already have an account? 
            <a href="login.php" class="text-blue-500 underline hover:text-blue-700">Login here</a>.
        </p>
    </form>
</body>
</html>
