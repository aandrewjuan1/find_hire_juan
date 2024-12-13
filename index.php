<?php
session_start();
require_once('./Models/JobPost.php');

// Check if user is authenticated; if not, redirect to login.php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$roleID = $_SESSION['role'];
$jobPost = new JobPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job_post_id'])) {
    $jobPostId = $_POST['delete_job_post_id'];
    if ($jobPost->deleteJobPost($jobPostId)) {
        $_SESSION['message'] = ['type' => 'success', 'content' => 'Job post deleted successfully.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'content' => 'Failed to delete the job post.'];
    }
    header("Location: index.php");
    exit;
}

$jobPosts = $jobPost->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage - Job Posts</title>
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

    <!-- Main Content -->
    <main class="flex-grow max-w-screen-xl mt-20 mx-auto px-6 py-8">
        <div class="text-4xl font-bold text-blue-600 mb-8 flex justify-between items-center">
            Job Posts

            <!-- Grouping the buttons -->
            <div class="flex space-x-4">
                <?php if ($roleID == 2): ?>
                    <a href="create_job.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md transition duration-200 text-base">
                        Create Job Post
                    </a>
                <?php endif; ?>
                <?php if ($roleID == 1): ?>
                    <a href="messages_for_applicant.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md transition duration-200 text-base">
                        Message HR
                    </a>
                <?php elseif ($roleID == 2): ?>
                    <a href="messages_for_hr.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md transition duration-200 text-base">
                        View Messages
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Flash Message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-6 p-4 rounded-lg <?= $_SESSION['message']['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= htmlspecialchars($_SESSION['message']['content']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Job Posts List -->
        <?php if ($jobPosts): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($jobPosts as $post): ?>
                    <div class="p-6 bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                        <div class="mb-4">
                            <!-- Job Title with larger font -->
                            <h2 class="text-2xl font-semibold text-blue-700"><?= htmlspecialchars($post['Title']) ?></h2>
                        </div>
                        <p class="text-gray-600 my-3"><?= htmlspecialchars($post['Description']) ?></p>
                        <p class="text-xs text-gray-500 mb-4">Created by <?= htmlspecialchars($post['CreatedBy']) ?> on <?= htmlspecialchars($post['CreatedAt']) ?></p>

                        <!-- Buttons moved below -->
                        <div class="mt-4">
                            <?php if ($roleID == 2): ?>
                                <div class="flex space-x-4">
                                    <a href="applications.php?job_post_id=<?= htmlspecialchars($post['JobPostID']) ?>" class="bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2 rounded-lg whitespace-normal">
                                        Applications
                                    </a>
                                    <form action="index.php" method="POST" class="inline-block">
                                        <input type="hidden" name="delete_job_post_id" value="<?= htmlspecialchars($post['JobPostID']) ?>">
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            <?php elseif ($roleID == 1): ?>
                                <div class="flex justify-center">
                                    <a href="apply_for_a_job.php?job_post_id=<?= htmlspecialchars($post['JobPostID']) ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg">
                                        Apply
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">No job posts available.</p>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-blue-600 text-white py-4 mt-8">
        <div class="text-center">
            <p>&copy; <?= date('Y') ?> FindHire. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
