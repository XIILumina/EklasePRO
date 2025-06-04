<?php
// views/classes/index.view.php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>


        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <div class="mb-6">
                <a href="/classes/create" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                    Create New Class
                </a>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-4">Class Name</th>
                            <th class="p-4">Students</th>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                <th class="p-4">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($classes)): ?>
                            <tr>
                                <td colspan="<?php echo $_SESSION['user']['role'] === 'admin' ? 3 : 2; ?>" class="p-4 text-center text-gray-500">
                                    No classes found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($classes as $class): ?>
                                <tr class="border-t">
                                    <td class="p-4"><?php echo htmlspecialchars($class['class_name']); ?></td>
                                    <td class="p-4">
                                        <?php if (empty($class['students'])): ?>
                                            No students
                                        <?php else: ?>
                                            <?php foreach ($class['students'] as $student): ?>
                                                <p><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                        <td class="p-4">
                                            <a href="/classes/<?php echo $class['id']; ?>/edit" class="text-blue-500 hover:underline">Edit</a>
                                            <a href="/classes/<?php echo $class['id']; ?>/add-students" class="text-green-500 hover:underline ml-2">Add Students</a>
                                            <form action="/classes/<?php echo $class['id']; ?>/delete" method="POST" class="inline">
                                                <button type="submit" class="text-red-500 hover:underline ml-2" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    <?php endif; ?>
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