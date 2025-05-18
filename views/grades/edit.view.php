<?php
// views/grades/edit.view.php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4 max-w-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>

        <form action="/grades/<?php echo $grade['id']; ?>/update" method="POST" class="space-y-5">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($grade['id']); ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="class_id">Class</label>
                <select name="class_id" id="class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo $class['id'] == $grade['class_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="student_id">Student</label>
                <select name="student_id" id="student_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>" <?php echo $student['id'] == $grade['student_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="lesson_id">Lesson</label>
                <select name="lesson_id" id="lesson_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <?php foreach ($lessons as $lesson): ?>
                        <option value="<?php echo $lesson['id']; ?>" <?php echo $lesson['id'] == $grade['lesson_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lesson['lesson_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="grade_value">Grade (0-100)</label>
                <input type="number" name="grade_value" id="grade_value" value="<?php echo htmlspecialchars($grade['grade_value']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" min="0" max="100" step="0.01" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="grade_date">Date</label>
                <input type="date" name="grade_date" id="grade_date" value="<?php echo htmlspecialchars($grade['grade_date']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="comments">Comments</label>
                <textarea name="comments" id="comments" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" rows="4"><?php echo htmlspecialchars($grade['comments'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="w-full py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                Update Grade
            </button>
            <a href="/grades?class_id=<?php echo $grade['class_id']; ?>" class="block text-center text-blue-500 hover:underline">Cancel</a>
        </form>
    </div>
</div>

<?php component('footer'); ?>