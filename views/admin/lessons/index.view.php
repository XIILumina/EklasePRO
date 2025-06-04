<?php component('header'); ?>

<div class="min-h-screen bg-black text-red-400 p-8">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-red-500">Lessons</h1>

        <a href="/lessons/create" class="bg-red-600 hover:bg-red-500 text-white font-semibold px-4 py-2 rounded-lg mb-4 inline-block">
            + New Lesson
        </a>

        <?php if (!empty($lessons)): ?>
            <div class="space-y-4">
                <?php foreach ($lessons as $lesson): ?>
                    <div class="bg-gray-900 rounded-xl p-4 border border-red-700 shadow">
                        <h2 class="text-xl font-semibold"><?= htmlspecialchars($lesson['lesson_name']) ?></h2>
                        <?php if (!empty($lesson['description'])): ?>
                            <p class="text-sm text-gray-300 mt-1"><?= htmlspecialchars($lesson['description']) ?></p>
                        <?php endif; ?>
                        <div class="mt-2 text-sm">
                            <strong class="text-red-400">Assignments:</strong>
                            <ul class="list-disc list-inside text-gray-300">
                                <?php foreach ($lesson['assignments'] as $assignment): ?>
                                    <li><?= htmlspecialchars($assignment['class_name']) ?> — <?= htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="mt-4 flex gap-3">
                            <a href="/lessons/<?= $lesson['id'] ?>/edit" class="text-sm bg-red-700 hover:bg-red-600 text-white px-3 py-1 rounded">Edit</a>
                            <form method="POST" action="/lessons/<?= $lesson['id'] ?>/delete" onsubmit="return confirm('Are you sure?');">
                                <button type="submit" class="text-sm bg-black border border-red-700 text-red-400 px-3 py-1 rounded hover:bg-red-800">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-400">No lessons found.</p>
        <?php endif; ?>
    </div>
</div>

<?php component('footer'); ?>
