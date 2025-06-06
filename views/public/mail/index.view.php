<?php
// views/mail/index.view.php
component('header');
?>

<div class="min-h-screen bg-black text-white py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <h1 class="text-3xl font-bold text-red-400 mb-6"><?php echo htmlspecialchars($title); ?></h1>

        <div class="mb-6">
            <a href="/mail/create" class="py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                Compose Mail
            </a>
        </div>

        <div class="bg-gray-900 rounded-2xl shadow-xl p-6">
            <h2 class="text-xl font-semibold text-red-300 mb-4">Mailbox</h2>

            <?php
            // Merge and sort all mails by date (newest first)
            $allMail = array_merge(
                array_map(fn($m) => $m + ['direction' => 'inbox'], $inbox),
                array_map(fn($m) => $m + ['direction' => 'sent'], $sent)
            );
            usort($allMail, fn($a, $b) => strtotime($b['sent_at']) <=> strtotime($a['sent_at']));
            ?>

            <?php if (empty($allMail)): ?>
                <p class="text-gray-400 text-center">No messages yet.</p>
            <?php else: ?>
                <ul class="space-y-4">
                    <?php foreach ($allMail as $mail): ?>
                        <li class="rounded-xl p-4 shadow-md <?php echo $mail['direction'] === 'sent' ? 'bg-red-800/30 text-right' : 'bg-gray-800/40'; ?>">
                            <div class="text-sm text-gray-400 mb-1">
                                <?php if ($mail['direction'] === 'sent'): ?>
                                    You → <?php echo htmlspecialchars($mail['first_name'] . ' ' . $mail['last_name']); ?>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($mail['first_name'] . ' ' . $mail['last_name']); ?> → You
                                <?php endif; ?>
                            </div>
                            <div class="text-lg font-semibold text-red-300">
                                <a href="/mail/<?php echo $mail['id']; ?>" class="hover:underline">
                                    <?php echo htmlspecialchars($mail['subject']); ?>
                                </a>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <?php echo date("Y-m-d H:i", strtotime($mail['sent_at'])); ?>
                                <?php if ($mail['direction'] === 'inbox'): ?>
                                    · <?php echo $mail['is_read'] ? 'Read' : 'Unread'; ?>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php component('footer'); ?>
