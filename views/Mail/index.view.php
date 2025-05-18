<?php
// views/mail/index.view.php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>


        <div class="mb-6">
            <a href="/mail/create" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                Compose Mail
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Inbox</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-4">From</th>
                            <th class="p-4">Subject</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inbox)): ?>
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">No messages in inbox.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inbox as $mail): ?>
                                <tr class="border-t">
                                    <td class="p-4"><?php echo htmlspecialchars($mail['first_name'] . ' ' . $mail['last_name']); ?></td>
                                    <td class="p-4">
                                        <a href="/mail/<?php echo $mail['id']; ?>" class="text-blue-500 hover:underline">
                                            <?php echo htmlspecialchars($mail['subject']); ?>
                                        </a>
                                    </td>
                                    <td class="p-4"><?php echo htmlspecialchars($mail['sent_at']); ?></td>
                                    <td class="p-4"><?php echo $mail['is_read'] ? 'Read' : 'Unread'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Sent</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-4">To</th>
                            <th class="p-4">Subject</th>
                            <th class="p-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sent)): ?>
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-500">No sent messages.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sent as $mail): ?>
                                <tr class="border-t">
                                    <td class="p-4"><?php echo htmlspecialchars($mail['first_name'] . ' ' . $mail['last_name']); ?></td>
                                    <td class="p-4">
                                        <a href="/mail/<?php echo $mail['id']; ?>" class="text-blue-500 hover:underline">
                                            <?php echo htmlspecialchars($mail['subject']); ?>
                                        </a>
                                    </td>
                                    <td class="p-4"><?php echo htmlspecialchars($mail['sent_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php component('footer'); ?>