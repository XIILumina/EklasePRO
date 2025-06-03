<?php
component('header');
?>

<div class="min-h-screen bg-gray-950 text-white py-8">
    <div class="container mx-auto px-4 max-w-md">
        <h1 class="text-3xl font-bold text-red-400 mb-6"><?php echo htmlspecialchars($title); ?></h1>

        <form action="/mail/store" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1" for="receiver_id">To</label>
                <select name="receiver_id" id="receiver_id" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required>
                    <option value="">Select a recipient</option>
                    <?php foreach ($recipients as $recipient): ?>
                        <option value="<?php echo $recipient['id']; ?>">
                            <?php echo htmlspecialchars($recipient['first_name'] . ' ' . $recipient['last_name'] . ' (' . $recipient['role'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1" for="subject">Subject</label>
                <input type="text" name="subject" id="subject" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1" for="body">Message</label>
                <textarea name="body" id="body" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500" rows="6" required></textarea>
            </div>

            <button type="submit" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                Send Mail
            </button>
            <a href="/mail" class="block text-center text-red-400 hover:underline">Cancel</a>
        </form>
    </div>
</div>

<?php component('footer'); ?>
