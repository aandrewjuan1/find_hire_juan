<?php
session_start();
require_once('./Models/JobPost.php');

// Restrict access to HR role (RoleID = 2)
if ($_SESSION['role'] !== 2) {
    header('Location: index.php');
    exit;
}

// Check if user is authenticated; if not, redirect to login.php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$jobPost = new JobPost();
$errors = [];
$successMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $createdBy = $_SESSION['user_id'];

    $result = $jobPost->store($title, $description, $createdBy);

    if ($result['success']) {
        header('Location: index.php');
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
    <title>Create Job Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #e2e8f0, #cbd5e1); /* Light blue gradient */
        }
    </style>
</head>
<body class="font-sans antialiased flex flex-col min-h-screen bg-gradient-to-b from-blue-100 to-blue-200">

    <!-- Header / Navigation -->
    <header class="bg-white shadow-lg rounded-lg fixed top-0 left-0 right-0 z-50">
        <div class="max-w-screen-xl mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4V1m0 22v-3M4 12H1m22 0h-3M6.343 6.343l-1.414 1.414M17.071 17.071l-1.414 1.414M6.343 17.071l1.414-1.414M17.071 6.343l1.414-1.414" />
                </svg>
                <span class="text-3xl font-extrabold text-blue-700">FindHire</span>
            </div>

            <div class="flex items-center space-x-6">
                <span class="text-gray-700">Welcome, <?= htmlspecialchars($_SESSION['email']) ?></span>
                <form action="logout.php" method="POST" class="inline-block">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-6 py-8 mt-24">
        <!-- Centered Container -->
        <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg mx-auto">
            <!-- Title Section -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-blue-600">Create Job Post</h2>

                <!-- Back Button -->
                <div>
                    <a href="index.php" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-full transition duration-200">Back</a>
                </div>
            </div>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <ul class="mb-6 text-red-600">
                    <?php foreach ($errors as $error): ?>
                        <li class="mb-1"><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <!-- Form -->
            <form action="" method="POST">
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
                    <input type="text" name="title" id="title" class="w-full border border-blue-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" class="w-full border border-blue-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4" required></textarea>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full w-full shadow-md transition duration-200">Create Job Post</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-blue-600 text-white py-4 mt-8">
        <div class="text-center">
            <p>&copy; <?= date('Y') ?> FindHire. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
