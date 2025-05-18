<?php
// views/detentions/index.view.php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>

        <?php if (in_array($_SESSION['user']['role'], ['teacher', 'admin'])): ?>
            <div class="mb-6">
                <a href="/detentions/create" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200">
                    Assign Detention
                </a>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <?php if ($_SESSION['user']['role'] === 'student'): ?>
                                <th class="p-4">Teacher</th>
                            <?php else: ?>
                                <th class="p-4">Student</th>
                            <?php endif; ?>
                            <th class="p-4">Reason</th>
                            <th class="p-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($detentions)): ?>
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-500">No detentions found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($detentions as $detention): ?>
                                <tr class="border-t">
                                    <td class="p-4"><?php echo htmlspecialchars($detention['first_name'] . ' ' . $detention['last_name']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($detention['reason']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($detention['detention_date']); ?></td>
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