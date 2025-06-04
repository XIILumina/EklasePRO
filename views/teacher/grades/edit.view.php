<?php component('header'); ?>

<div class="flex items-center justify-center min-h-screen bg-black text-red-500 px-4">
    <div class="w-full max-w-md p-8 bg-gray-900 rounded-2xl shadow-2xl ring-1 ring-red-600">
        <h2 class="text-2xl font-bold text-center mb-6 text-red-400"><?php echo htmlspecialchars($title); ?></h2>
        <form action="/grades/<?php echo $grade['id']; ?>/update" method="POST" class="space-y-5">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($grade['id']); ?>">

            <div>
                <label class="block text-sm mb-1" for="class_id">Class</label>
                <select name="class_id" id="class_id" required
                    class="w-full px-4 py-2 bg-black border border-red-700 rounded-lg text-white focus:outline-none">
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo $class['id'] == $grade['class_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1" for="student_id">Student</label>
                <select name="student_id" id="student_id" required
                    class="w-full px-4 py-2 bg-black border border-red-700 rounded-lg text-white focus:outline-none">
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>" <?php echo $student['id'] == $grade['student_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1" for="lesson_id">Lesson</label>
                <select name="lesson_id" id="lesson_id" required
                    class="w-full px-4 py-2 bg-black border border-red-700 rounded-lg text-white focus:outline-none">
                    <?php foreach ($lessons as $lesson): ?>
                        <option value="<?php echo $lesson['id']; ?>" <?php echo $lesson['id'] == $grade['lesson_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lesson['lesson_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1" for="grade_value">Grade</label>
                <input type="number" name="grade_value" id="grade_value" min="0" max="100" step="0.01"
                    value="<?php echo htmlspecialchars($grade['grade_value']); ?>"
                    class="w-full px-4 py-2 bg-black border border-red-700 rounded-lg text-white focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm mb-1" for="grade_date">Date</label>
                <input type="date" name="grade_date" id="grade_date"
                    value="<?php echo htmlspecialchars($grade['grade_date']); ?>"
                    class="w-full px-4 py-2 bg-black border border-red-700 rounded-lg text-white focus:outline-none" required>
            </div>

            <button type="submit"
                class="w-full py-2 px-4 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition duration-150">
                Update Grade
            </button>
            <a href="/grades?class_id=<?php echo $grade['class_id']; ?>" class="block text-center text-red-400 hover:underline">Cancel</a>
        </form>
    </div>
</div>

<?php component('footer'); ?>
