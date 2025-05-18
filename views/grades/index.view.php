<?php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>


        <?php if ($_SESSION['user']['role'] !== 'student'): ?>
            <div class="mb-6">
                <form action="/grades" method="GET" class="flex items-center space-x-4">
                    <select name="class_id" id="class_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" <?php echo $class_id == $class['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($class['class_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                        Filter
                    </button>
                    <?php if ($class_id): ?>
                        <a href="/grades/create?class_id=<?php echo $class_id; ?>" class="py-2 px-4 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                            Add Grade
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <?php if ($_SESSION['user']['role'] === 'admin' && $grades && $class_id): ?>
                <form action="/grades/bulk-update" method="POST">
                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="p-4">Student</th>
                                    <th class="p-4">Class</th>
                                    <th class="p-4">Lesson</th>
                                    <th class="p-4">Grade</th>
                                    <th class="p-4">Date</th>
                                    <th class="p-4">Comments</th>
                                    <th class="p-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grades as $index => $grade): ?>
                                    <tr class="border-t">
                                        <td class="p-4">
                                            <?php echo htmlspecialchars($grade['first_name'] . ' ' . $grade['last_name']); ?>
                                            <input type="hidden" name="grades[<?php echo $index; ?>][id]" value="<?php echo $grade['id']; ?>">
                                            <input type="hidden" name="grades[<?php echo $index; ?>][student_id]" value="<?php echo $grade['student_id']; ?>">
                                            <input type="hidden" name="grades[<?php echo $index; ?>][class_id]" value="<?php echo $grade['class_id']; ?>">
                                            <input type="hidden" name="grades[<?php echo $index; ?>][lesson_id]" value="<?php echo $grade['lesson_id']; ?>">
                                        </td>
                                        <td class="p-4"><?php echo htmlspecialchars($grade['class_name']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($grade['lesson_name']); ?></td>
                                        <td class="p-4">
                                            <input type="number" name="grades[<?php echo $index; ?>][grade_value]" value="<?php echo $grade['grade_value']; ?>" class="w-20 px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" min="0" max="100" step="0.01" required>
                                        </td>
                                        <td class="p-4">
                                            <input type="date" name="grades[<?php echo $index; ?>][grade_date]" value="<?php echo $grade['grade_date']; ?>" class="px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                        </td>
                                        <td class="p-4">
                                            <input type="text" name="grades[<?php echo $index; ?>][comments]" value="<?php echo htmlspecialchars($grade['comments'] ?? ''); ?>" class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </td>
                                        <td class="p-4">
                                            <a href="/grades/<?php echo $grade['id']; ?>/edit" class="text-blue-500 hover:underline">Edit</a>
                                            <form action="/grades/<?php echo $grade['id']; ?>/delete" method="POST" class="inline">
                                                <button type="submit" class="text-red-500 hover:underline ml-2" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($grades): ?>
                        <button type="submit" class="mt-4 py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                            Save All Changes
                        </button>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-4">Student</th>
                                <th class="p-4">Class</th>
                                <th class="p-4">Lesson</th>
                                <th class="p-4">Grade</th>
                                <th class="p-4">Date</th>
                                <th class="p-4">Comments</th>
                                <?php if ($_SESSION['user']['role'] !== 'student'): ?>
                                    <th class="p-4">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($grades)): ?>
                                <tr>
                                    <td colspan="<?php echo $_SESSION['user']['role'] !== 'student' ? 7 : 6; ?>" class="p-4 text-center text-gray-500">
                                        No grades found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($grades as $grade): ?>
                                    <tr class="border-t">
                                        <td class="p-4"><?php echo htmlspecialchars($grade['first_name'] . ' ' . $grade['last_name']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($grade['class_name']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($grade['lesson_name']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($grade['grade_value']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($grade['grade_date']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($grade['comments'] ?? 'None'); ?></td>
                                        <?php if ($_SESSION['user']['role'] !== 'student'): ?>
                                            <td class="p-4">
                                                <a href="/grades/<?php echo $grade['id']; ?>/edit" class="text-blue-500 hover:underline">Edit</a>
                                                <form action="/grades/<?php echo $grade['id']; ?>/delete" method="POST" class="inline">
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
            <?php endif; ?>
        </div>
    </div>
</div>

<?php component('footer'); ?>