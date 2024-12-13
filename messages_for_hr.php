<?php
session_start();
require_once('./Models/Message.php');
require_once('./Models/User.php');

// Check if user is authenticated; if not, redirect to login.php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Restrict access only to HR users
if ($_SESSION['role'] !== 2) {
    header('Location: index.php');
    exit;
}

$messageModel = new Message();
$userModel = new User();

// Fetch all applicants to show in dropdown
$applicants = $userModel->getApplicants();
$sendSuccess = false;
$deleteSuccess = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle message sending
    if (isset($_POST['send_message'])) {
        $receiverId = $_POST['receiver_id'] ?? 0;
        $content = $_POST['content'] ?? '';
        if ($receiverId && $content) {
            $sendSuccess = $messageModel->sendMessage($_SESSION['user_id'], $receiverId, $content);
            header('Location: messages_for_hr.php');
            exit;
        }
    }

    // Handle delete all messages
    if (isset($_POST['delete_messages'])) {
        $applicantId = $_POST['applicant_id'] ?? 0;
        if ($applicantId) {
            $deleteSuccess = $messageModel->deleteAllMessages($_SESSION['user_id'], $applicantId);
            header('Location: messages_for_hr.php');
            exit;
        }
    }
}

// Fetch all conversations grouped by applicant (ReceiverID)
$conversations = [];
foreach ($applicants as $applicant) {
    $conversation = $messageModel->getConversationBetweenUserAndApplicant($_SESSION['user_id'], $applicant['UserID']);
    
    // Only add applicant to the conversations array if there are messages
    if (count($conversation) > 0) {
        $conversations[$applicant['UserID']] = $conversation;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversations with Applicants</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #e2e8f0, #cbd5e1); /* Light blue gradient */
        }
        .timestamp {
            color: #000; /* Make the timestamp more visible by changing the color to black */
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <!-- Header -->
    <!-- Header -->
    <header class="bg-white shadow-lg rounded-lg fixed top-0 left-0 right-0 z-50">
        <div class="max-w-screen-xl mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="index.php" class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4V1m0 22v-3M4 12H1m22 0h-3M6.343 6.343l-1.414 1.414M17.071 17.071l-1.414 1.414M6.343 17.071l1.414-1.414M17.071 6.343l1.414-1.414" />
                </svg>
                <span class="text-3xl font-extrabold text-blue-700">FindHire</span>
            </a>

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
        <h1 class="text-3xl font-bold text-blue-600 mb-6 flex justify-center">
            Conversations with Applicants
        </h1>

        <!-- Success Messages -->
        <?php if ($sendSuccess): ?>
            <div class="mb-6 p-4 rounded bg-blue-100 text-blue-700">
                Your message has been sent!
            </div>
        <?php endif; ?>
        <?php if ($deleteSuccess): ?>
            <div class="mb-6 p-4 rounded bg-red-100 text-red-700">
                All messages with this applicant have been deleted.
            </div>
        <?php endif; ?>

        <!-- Conversations Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (count($conversations) > 0): ?>
            <?php foreach ($conversations as $applicantId => $conversation): ?>
                <div class="message-list p-4 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col max-h-[500px]">
                    <!-- Message Header -->
                    <div class="message-header flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-blue-700 hover:text-blue-600"><?= htmlspecialchars($applicants[array_search($applicantId, array_column($applicants, 'UserID'))]['Username']) ?></h4>

                        <!-- Delete Conversation Button (with 'X' icon) -->
                        <form method="POST" class="inline-block">
                            <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($applicantId) ?>">
                            <button type="submit" name="delete_messages" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full transition duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Chat Bubbles -->
                    <div class="messages space-y-4 flex-grow max-h-[300px] overflow-y-auto mb-4">
                        <?php foreach ($conversation as $message): ?>
                            <div class="flex items-start <?= $message['SenderID'] == $_SESSION['user_id'] ? 'justify-end' : 'justify-start' ?>">
                                <div class="max-w-xs <?= $message['SenderID'] == $_SESSION['user_id'] ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' ?> p-4 rounded-xl shadow-md flex flex-col">
                                    <div class="flex justify-between items-center">
                                        <div class="font-semibold text-sm"><?= $message['SenderID'] == $_SESSION['user_id'] ? 'You' : 'Applicant' ?></div>
                                        <span class="timestamp text-xs text-gray-500"><?= date('H:i', strtotime($message['SentAt'])) ?></span>
                                    </div>
                                    <div class="mt-1 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($message['Content'])) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Message Input (fixed at bottom) -->
                    <form method="POST" class="flex items-center space-x-2 mt-auto">
                        <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($applicantId) ?>">
                        <textarea name="content" rows="2" class="w-full bg-blue-50 border border-blue-200 text-blue-600 rounded-md p-2" placeholder="Type a message..." required></textarea>
                        <button type="submit" name="send_message" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md shadow-md">Send</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-gray-500">No conversations yet. Start a message with an applicant!</p>
        <?php endif; ?>
    </div>


        <!-- Start New Conversation Section -->
        <div class="form-section mt-8">
            <h3 class="text-2xl font-semibold text-blue-600 mb-4">Start a New Conversation</h3>
            <form method="POST" class="bg-white p-6 rounded-lg shadow-md">
                <label for="receiver_id" class="text-blue-600 font-medium">Select Applicant:</label>
                <select name="receiver_id" id="receiver_id" class="block w-full bg-blue-50 border border-blue-200 text-blue-600 rounded-md p-2 mb-4" required>
                    <option value="" disabled selected>Select an applicant</option>
                    <?php foreach ($applicants as $applicant): ?>
                        <option value="<?= htmlspecialchars($applicant['UserID']) ?>"><?= htmlspecialchars($applicant['Username']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="content" class="text-blue-600 font-medium">Message Content:</label>
                <textarea name="content" id="content" rows="4" class="block w-full bg-blue-50 border border-blue-200 text-blue-600 rounded-md p-2 mb-4" required></textarea>

                <button type="submit" name="send_message" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md shadow-md">Send Message</button>
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
