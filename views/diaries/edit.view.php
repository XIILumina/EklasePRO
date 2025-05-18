<?php
// views/diaries/edit.view.php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4 max-w-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>

        <form action="/diaries/<?php echo $diary['id']; ?>/update" method="POST" class="space-y-5">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($diary['id']); ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="class_id">Class</label>
                <select name="class_id" id="class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo $class['id'] == $diary['class_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="lesson_id">Lesson</label>
                <select name="lesson_id" id="lesson_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <option value="">Select a lesson</option>
                    <?php foreach ($lessons as $lesson): ?>
                        <option value="<?php echo $lesson['id']; ?>" <?php echo $lesson['id'] == $diary['lesson_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lesson['lesson_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="teacher_id">Teacher</label>
                <select name="teacher_id" id="teacher_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <option value="">Select a teacher</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?php echo $teacher['id']; ?>" <?php echo $teacher['id'] == $diary['teacher_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="diary_date">Date</label>
                <input type="date" name="diary_date" id="diary_date" value="<?php echo htmlspecialchars($diary['diary_date']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="slot_number">Time Slot</label>
                <select name="slot_number" id="slot_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <?php foreach (Diary::getTimeSlots() as $slot => $time): ?>
                        <option value="<?php echo $slot; ?>" <?php echo $slot == $diary['slot_number'] ? 'selected' : ''; ?>>
                            <?php echo $time['start'] . ' - ' . $time['end']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="w-full py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                Update Entry
            </button>
            <a href="/diaries?class_id=<?php echo $diary['class_id']; ?>" class="block text-center text-blue-500 hover:underline">Cancel</a>
        </form>
    </div>
</div>

<?php component('footer'); ?>