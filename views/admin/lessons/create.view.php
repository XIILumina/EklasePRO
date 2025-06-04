<?php component('header'); ?>

<div class="min-h-screen bg-black text-red-400 p-8">
    <div class="max-w-3xl mx-auto bg-gray-900 p-6 rounded-xl shadow-xl ring-1 ring-red-600">
        <h2 class="text-2xl font-bold mb-4 text-red-400">Create Lesson</h2>

        <form method="POST" action="/lessons/store" class="space-y-5">
            <div>
                <label class="block text-sm mb-1">Lesson Name</label>
                <input type="text" name="lesson_name" required class="w-full px-4 py-2 bg-black border border-red-700 rounded text-white">
            </div>
            <div>
                <label class="block text-sm mb-1">Description (optional)</label>
                <textarea name="description" class="w-full px-4 py-2 bg-black border border-red-700 rounded text-white"></textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Class & Teacher Assignments</label>
                <?php foreach ($classes as $index => $class): ?>
                    <div class="flex gap-2 mb-2">
                        <select name="class_ids[]" class="bg-black border border-red-700 text-white rounded px-3 py-1 w-1/2">
                            <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                        </select>
                        <select name="teacher_ids[]" class="bg-black border border-red-700 text-white rounded px-3 py-1 w-1/2">
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="w-full py-2 bg-red-600 hover:bg-red-500 text-white font-semibold rounded">Create</button>
        </form>
    </div>
</div>

<?php component('footer'); ?>
