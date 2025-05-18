<?php
// views/mail/show.view.php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($mail['subject']); ?></h2>
            <p class="text-gray-600 mb-2">
                <strong>From:</strong> <?php echo htmlspecialchars($mail['first_name'] . ' ' . $mail['last_name']); ?>
            </p>
            <p class="text-gray-600 mb-4">
                <strong>Date:</strong> <?php echo htmlspecialchars($mail['sent_at']); ?>
            </p>
            <div class="border-t pt-4">
                <p class="text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($mail['body']); ?></p>
            </div>
        </div>

        <div class="mt-6">
            <a href="/mail" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                Back to Mailbox
            </a>
        </div>
    </div>
</div>

<?php component('footer'); ?>