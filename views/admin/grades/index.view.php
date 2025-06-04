<?php component('header'); ?>
<div class="min-h-screen bg-black text-red-500 px-4 pb-16">
    <div class="flex items-center justify-center w-full pt-10">
        <div class="w-full max-w-6xl p-6 bg-gray-900 rounded-2xl shadow-2xl ring-1 ring-red-600">
            <h2 class="text-2xl font-bold text-center mb-6 text-red-400">Grade Matrix</h2>
            <div class="mb-4 flex space-x-4">
                <form method="GET" action="/grades" class="flex-1">
                    <label for="class_id" class="block text-red-400 font-semibold mb-2">Class</label>
                    <select name="class_id" id="class_id" class="w-full bg-gray-800 text-white border border-red-600 rounded p-2" onchange="this.form.submit()">
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id']; ?>" <?= $class_id == $class['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($class['class_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <form method="GET" action="/grades" class="flex-1">
                    <input type="hidden" name="class_id" value="<?= $class_id; ?>">
                    <label for="lesson_id" class="block text-red-400 font-semibold mb-2">Lesson</label>
                    <select name="lesson_id" id="lesson_id" class="w-full bg-gray-800 text-white border border-red-600 rounded p-2" onchange="this.form.submit()">
                        <?php foreach ($lessons as $lesson): ?>
                            <option value="<?= $lesson['id']; ?>" <?= $lesson_id == $lesson['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($lesson['lesson_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <form method="GET" action="/grades" class="flex-1">
                    <input type="hidden" name="class_id" value="<?= $class_id; ?>">
                    <input type="hidden" name="lesson_id" value="<?= $lesson_id; ?>">
                    <label for="week" class="block text-red-400 font-semibold mb-2">Week</label>
                    <input type="date" name="week" id="week" value="<?= $week_start; ?>" class="w-full bg-gray-800 text-white border border-red-600 rounded p-2" onchange="this.form.submit()">
                </form>
            </div>
            <div class="mb-4 flex justify-between">
                <a href="/grades?class_id=<?= $class_id; ?>&lesson_id=<?= $lesson_id; ?>&week=<?= $prev_week; ?>" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-600">Previous Week</a>
                <a href="/grades?class_id=<?= $class_id; ?>&lesson_id=<?= $lesson_id; ?>&week=<?= $next_week; ?>" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-600">Next Week</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border-collapse table-fixed">
                    <thead>
                        <tr class="text-red-400 border-b border-red-600">
                            <th class="p-3 text-left w-48">Student</th>
                            <?php foreach ($weekDays as $day): ?>
                                <?php list($date, $dayName) = explode(' ', $day); ?>
                                <th class="p-3 text-center w-24 whitespace-nowrap"><?= $dayName; ?><br><?= $date; ?></th>
                            <?php endforeach; ?>
                            <th class="p-3 text-center w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student_id => $student): ?>
                            <tr class="border-t border-red-800 hover:bg-gray-800">
                                <td class="p-3 font-semibold text-red-300">
                                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                                </td>
                                <?php foreach ($weekDays as $day): ?>
                                    <?php $date = explode(' ', $day)[0]; ?>
                                    <td class="p-3 text-center">
                                        <?php if (isset($gradeMatrix[$student_id][$date])): ?>
                                            <span class="inline-block bg-red-800 text-white px-2 py-1 rounded">
                                                <?= $gradeMatrix[$student_id][$date]['value']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-500">–</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="p-3 text-center">
                                    <a href="/detentions/create?class_id=<?= $class_id; ?>&student_id=<?= $student_id; ?>" class="bg-yellow-600 text-white px-2 py-1 rounded hover:bg-yellow-700">Add Detention</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="<?= count($weekDays) + 2; ?>" class="text-center py-4 text-gray-500">
                                    No students found for this class.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-6">
                <a href="/grades/edit?class_id=<?= $class_id; ?>&lesson_id=<?= $lesson_id; ?>&week=<?= $week_start; ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit Grades</a>
                <a href="/grades/create?class_id=<?= $class_id; ?>&lesson_id=<?= $lesson_id; ?>" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 ml-4">Add New Grade</a>
            </div>
        </div>
    </div>
</div>
<?php component('footer'); ?>