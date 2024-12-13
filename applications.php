<?php
session_start();
require_once('./Models/Application.php');
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

// Fetch job post title
$jobPostModel = new JobPost();
$jobPostId = $_GET['job_post_id'] ?? 0;
$jobPostDetails = $jobPostModel->getJobPostById($jobPostId);
$jobPostTitle = $jobPostDetails['Title'] ?? "Unknown Job Post";
$roleID = $_SESSION['role'];

$applicationModel = new Application();
$applications = $applicationModel->getApplicationsByJobPostId($jobPostId);

// Handle form submission for accept/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $applicationId = $_POST['application_id'] ?? 0;

    if ($action === 'accept') {
        $applicationModel->acceptApplication($applicationId);
    } elseif ($action === 'reject') {
        $applicationModel->rejectApplication($applicationId);
    }

    header("Location: applications.php?job_post_id=" . $jobPostId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #e2e8f0, #cbd5e1);
        }
    </style>
</head>
<body class="font-sans antialiased flex flex-col min-h-screen bg-gradient-to-b from-blue-100 to-blue-200">

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

    <!-- Main Container -->
    <main class="flex-grow max-w-screen-xl mt-20 mx-auto px-6 py-8">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-6xl mx-auto">
            <!-- Title and Back Button -->
            <div class="flex justify-between items-center mb-8">
                <a href="index.php" 
                   class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-full shadow-md transition">
                    Back
                </a>
                <h2 class="text-2xl font-bold text-blue-700">Applications for: <?= htmlspecialchars($jobPostTitle) ?></h2>
            </div>

            <!-- Applications Section -->
            <div>
                <?php if ($applications && count($applications) > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($applications as $application): ?>
                            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                                <h3 class="text-xl font-semibold text-blue-700"><?= htmlspecialchars($application['Username']) ?></h3>
                                <p class="text-gray-600 mt-2"><?= nl2br(htmlspecialchars($application['CoverLetter'])) ?></p>
                                <div class="mt-4">
                                    <a href="<?= htmlspecialchars($application['ResumePath']) ?>" target="_blank" 
                                       class="text-blue-500 hover:text-blue-600 underline">
                                        View Resume
                                    </a>
                                </div>
                                <p class="text-sm text-gray-500 mt-2"><?= htmlspecialchars($application['Status']) ?></p>
                                
                                <form method="POST" class="mt-4 flex space-x-2">
                                    <input type="hidden" name="application_id" value="<?= htmlspecialchars($application['ApplicationID']) ?>">
                                    <button type="submit" name="action" value="accept" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md transition">
                                        Accept
                                    </button>
                                    <button type="submit" name="action" value="reject" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md transition">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-600">No applications found for this job post.</p>
                <?php endif; ?>
            </div>
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

