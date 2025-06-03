<?php component('header'); ?>

<div class="flex items-center justify-center min-h-screen bg-black text-red-500 px-4">
    <div class="w-full overflow-x-auto max-w-6xl p-6 bg-gray-900 rounded-2xl shadow-2xl ring-1 ring-red-600">
        <h2 class="text-2xl font-bold text-center mb-6 text-red-400">Grades Matrix</h2>
        <table class="min-w-full text-sm border-collapse table-fixed">
            <thead>
                <tr class="text-red-400 border-b border-red-600">
                    <th class="p-3 text-left w-48">Lesson</th>
                    <?php
                        // Always show September to June
                        $fixedMonths = ['September','October','November','December','January','February','March','April','May','June'];
                        foreach ($fixedMonths as $month):
                    ?>
                        <th class="p-3 text-center w-32 whitespace-nowrap"><?php echo $month; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Ensure all lessons from DB are shown, even with no grades
                    foreach ($lessonGrades as $lesson => $gradesByMonth):
                ?>
                    <tr class="border-t border-red-800 hover:bg-gray-800">
                        <td class="p-3 font-semibold text-red-300"><?php echo htmlspecialchars($lesson); ?></td>
                        <?php foreach ($fixedMonths as $month): ?>
                            <td class="p-3 text-center">
                                <?php if (!empty($gradesByMonth[$month])): ?>
                                    <?php
                                        // Show average or comma-separated grades
                                        echo '<span class="inline-block bg-red-800 text-white px-2 py-1 rounded">'
                                            . (is_array($gradesByMonth[$month]) ? implode(', ', $gradesByMonth[$month]) : $gradesByMonth[$month])
                                            . '</span>';
                                    ?>
                                <?php else: ?>
                                    <span class="text-gray-500">–</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>

                <?php if (isset($lessonGrades['Detention'])): ?>
                    <tr class="border-t border-red-800 hover:bg-gray-800">
                        <td class="p-3 font-semibold text-yellow-400">Detentions</td>
                        <?php foreach ($fixedMonths as $month): ?>
                            <td class="p-3 text-center">
                                <?php if (!empty($lessonGrades['Detention'][$month])): ?>
                                    <span class="inline-block bg-yellow-700 text-white px-2 py-1 rounded">
                                        <?php echo is_array($lessonGrades['Detention'][$month]) ? implode(', ', $lessonGrades['Detention'][$month]) : $lessonGrades['Detention'][$month]; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-500">–</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>

                <?php if (empty($lessonGrades)): ?>
                    <tr>
                        <td colspan="<?php echo count($fixedMonths) + 1; ?>" class="text-center py-4 text-gray-500">
                            No grades available to display.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php component('footer'); ?>
