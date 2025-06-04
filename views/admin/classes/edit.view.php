<?php
// views/classes/edit.view.php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4 max-w-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>


        <form action="/classes/<?php echo $class['id']; ?>/update" method="POST" class="space-y-5">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($class['id']); ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="class_name">Class Name</label>
                <input type="text" name="class_name" id="class_name" value="<?php echo htmlspecialchars($class['class_name']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <button type="submit" class="w-full py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                Update Class
            </button>
            <a href="/classes" class="block text-center text-blue-500 hover:underline">Cancel</a>
        </form>
    </div>
</div>

<?php component('footer'); ?>