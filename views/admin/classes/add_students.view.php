<?php
// views/classes/add_students.view.php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4 max-w-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>

        <form action="/classes/<?php echo $class['id']; ?>/store-students" method="POST" class="space-y-5">
            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class['id']); ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Students</label>
                <?php if (empty($students)): ?>
                    <p class="text-gray-500">No available students to add.</p>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>" id="student_<?php echo $student['id']; ?>" class="mr-2">
                            <label for="student_<?php echo $student['id']; ?>" class="text-gray-700">
                                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($students)): ?>
                <button type="submit" class="w-full py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                    Add Students
                </button>
            <?php endif; ?>
            <a href="/classes" class="block text-center text-blue-500 hover:underline">Cancel</a>
        </form>
    </div>
</div>

<?php component('footer'); ?>