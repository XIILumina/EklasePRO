<?php
// views/diaries/index.view.php
component('header');
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($title); ?></h1>

        <!-- Calendar Navigation -->
        <div class="flex justify-between items-center mb-6">
            <a href="/diaries?class_id=<?php echo $class_id; ?>&week=<?php echo (new \DateTime($start_date))->modify('-1 week')->format('Y-m-d'); ?>" class="text-blue-500 hover:underline">Previous Week</a>
            <span class="text-gray-700 font-semibold"><?php echo (new \DateTime($start_date))->format('F j, Y'); ?> - <?php echo (new \DateTime($start_date))->modify('+4 days')->format('F j, Y'); ?></span>
            <a href="/diaries?class_id=<?php echo $class_id; ?>&week=<?php echo (new \DateTime($start_date))->modify('+1 week')->format('Y-m-d'); ?>" class="text-blue-500 hover:underline">Next Week</a>
        </div>

        <!-- Weekly Schedule -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-4">Time</th>
                            <th class="p-4">Monday</th>
                            <th class="p-4">Tuesday</th>
                            <th class="p-4">Wednesday</th>
                            <th class="p-4">Thursday</th>
                            <th class="p-4">Friday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($time_slots as $slot => $time): ?>
                            <tr class="border-t">
                                <td class="p-4"><?php echo $time['start'] . ' - ' . $time['end']; ?></td>
                                <?php for ($day = 0; $day < 5; $day++): ?>
                                    <?php
                                    $current_date = (new \DateTime($start_date))->modify("+$day days")->format('Y-m-d');
                                    $entry = array_filter($diary, fn($e) => $e['diary_date'] === $current_date && $e['slot_number'] == $slot);
                                    $entry = reset($entry);
                                    ?>
                                    <td class="p-4">
                                        <?php if ($entry): ?>
                                            <div class="text-sm">
                                                <p class="font-semibold"><?php echo htmlspecialchars($entry['lesson_name']); ?></p>
                                                <p class="text-gray-600"><?php echo htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']); ?></p>
                                                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                                    <a href="/diaries/<?php echo $entry['id']; ?>/edit" class="text-blue-500 hover:underline text-xs">Edit</a>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-gray-400">No lesson</p>
                                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                                <a href="/diaries/create?class_id=<?php echo $class_id; ?>&diary_date=<?php echo $current_date; ?>&slot_number=<?php echo $slot; ?>" class="text-blue-500 hover:underline text-xs">Add</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php component('footer'); ?>