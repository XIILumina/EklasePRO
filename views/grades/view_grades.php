<?php component('header'); ?>

<div class="container mx-auto p-6">
    <h2 class="text-xl font-semibold">Your Grades</h2>
    <?php
    $studentId = $_SESSION['user']['id']; // Get current student's ID from the session
    $grades = Grade::getGrades($studentId); // Get grades using the controller method
    if (empty($grades)) {
        echo "<p>You have no grades.</p>";
    } else {
        echo "<ul>";
        foreach ($grades as $grade) {
            echo "<li>Subject: " . htmlspecialchars($grade->subject_name) . ", Grade: " . htmlspecialchars($grade->grade) . ", Date: " . htmlspecialchars($grade->date) . "</li>";
        }
        echo "</ul>";
    }
    ?>
</div>

<?php component('footer'); ?>
