<?php component('header'); ?>

<div class="min-h-screen bg-black text-white py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        <h1 class="text-3xl font-bold text-red-400 mb-6"><?php echo htmlspecialchars($title); ?></h1>

        <div class="bg-gray-900 rounded-2xl shadow-xl p-6">
            <h2 class="text-2xl font-semibold text-red-300 mb-2"><?php echo htmlspecialchars($mail['subject']); ?></h2>
            <p class="text-sm text-gray-400 mb-1">
                <strong>From:</strong> <?php echo htmlspecialchars($mail['first_name'] . ' ' . $mail['last_name']); ?>
            </p>
            <p class="text-sm text-gray-500 mb-4">
                <strong>Date:</strong> <?php echo htmlspecialchars($mail['sent_at']); ?>
            </p>

            <div class="border-t border-gray-700 pt-4">
                <p class="whitespace-pre-wrap text-gray-300"><?php echo htmlspecialchars($mail['body']); ?></p>
            </div>
        </div>

        <div class="mt-6 flex space-x-4">
            <a href="/mail" class="py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                Back to Mailbox
            </a>
            <?php if ($mail['receiver_id'] == $_SESSION['user']['id']): ?>
                <a href="/mail/create?reply_to=<?php echo $mail['id']; ?>" class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                    Reply
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php component('footer'); ?>