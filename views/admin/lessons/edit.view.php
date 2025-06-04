<?php component('header'); ?>

<div class="min-h-screen bg-black text-red-400 p-8">
    <div class="max-w-3xl mx-auto bg-gray-900 p-6 rounded-xl shadow-xl ring-1 ring-red-600">
        <h2 class="text-2xl font-bold mb-4 text-red-400">Edit Lesson</h2>

        <form method="POST" action="/lessons/<?= $lesson['id'] ?>/update" class="space-y-5">
            <input type="hidden" name="id" value="<?= $lesson['id'] ?>">
            <div>
                <label class="block text-sm mb-1">Lesson Name</label>
                <input type="text" name="lesson_name" value="<?= htmlspecialchars($lesson['lesson_name']) ?>" required class="w-full px-4 py-2 bg-black border border-red-700 rounded text-white">
            </div>
            <div>
                <label class="block text-sm mb-1">Description</label>
                <textarea name="description" class="w-full px-4 py-2 bg-black border border-red-700 rounded text-white"><?= htmlspecialchars($lesson['description']) ?></textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Class & Teacher Assignments</label>
                <?php foreach ($classes as $class): ?>
                    <div class="flex gap-2 mb-2">
                        <select name="class_ids[]" class="bg-black border border-red-700 text-white rounded px-3 py-1 w-1/2">
                            <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                        </select>
                        <select name="teacher_ids[]" class="bg-black border border-red-700 text-white rounded px-3 py-1 w-1/2">
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"
                                    <?php foreach ($assignments as $a): if ($a['class_id'] == $class['id'] && $a['teacher_id'] == $teacher['id']): ?>selected<?php endif; endforeach; ?>>
                                    <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="w-full py-2 bg-red-600 hover:bg-red-500 text-white font-semibold rounded">Update</button>
        </form>
    </div>
</div>

<?php component('footer'); ?>
