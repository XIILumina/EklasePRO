<?php
component('header');

// Prepare data: group grades by lesson and month
$monthNames = [];
$lessonGrades = [];

foreach ($grades as $grade) {
    $month = date('F', strtotime($grade['grade_date']));
    $lesson = $grade['lesson_name'];
    $monthNames[$month] = true;

    $lessonGrades[$lesson][$month] = $grade['grade_value'];
}

// Sort months in calendar order
$monthOrder = array_map(
    fn($i) => date('F', mktime(0, 0, 0, $i, 10)),
    range(1, 12)
);
$sortedMonths = array_values(array_filter($monthOrder, fn($m) => isset($monthNames[$m])));
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 via-purple-100 to-black-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Your Grades by Lesson and Month</h1>

        <div class="overflow-x-auto bg-white shadow-2xl rounded-2xl p-6">
            <table class="table-auto w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="bg-blue-200 text-gray-800 uppercase tracking-wide">
                        <th class="p-3 font-semibold text-base">Lesson</th>
                        <?php foreach ($sortedMonths as $month): ?>
                            <th class="p-3 font-semibold text-base text-center"><?php echo $month; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lessonGrades as $lesson => $gradesByMonth): ?>
                        <tr class="border-t border-gray-300">
                            <td class="p-3 font-medium text-gray-700"><?php echo htmlspecialchars($lesson); ?></td>
                            <?php foreach ($sortedMonths as $month): ?>
                                <td class="p-3 text-center">
                                    <?php if (isset($gradesByMonth[$month])): ?>
                                        <span class="inline-block bg-green-100 text-green-700 font-semibold px-3 py-1 rounded-full">
                                            <?php echo $gradesByMonth[$month]; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">–</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($lessonGrades)): ?>
                        <tr>
                            <td colspan="<?php echo count($sortedMonths) + 1; ?>" class="text-center py-4 text-gray-500">
                                No grades available to display.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php component('footer'); ?>
