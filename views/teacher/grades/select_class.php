<?php
component('header'); ?>
<div class="min-h-screen bg-black text-red-500 px-4 pb-16">
    <div class="flex items-center justify-center w-full pt-10">
        <div class="w-full max-w-6xl p-6 bg-gray-900 rounded-2xl shadow-2xl ring-1 ring-red-600">
            <h2 class="text-2xl font-bold text-center mb-6 text-red-400">Select Class</h2>
            <form method="GET" action="/grades">
                <div class="mb-4">
                    <label for="class_id" class="block text-red-400 font-semibold mb-2">Class</label>
                    <select name="class_id" id="class_id" class="w-full bg-gray-800 text-white border border-red-600 rounded p-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Select a class</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id']; ?>"><?= htmlspecialchars($class['class_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Select</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php component('footer'); ?>