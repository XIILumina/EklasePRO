<?php
// File: views/teacher/classes.php
?>
<h1>My Classes</h1>
<ul>
    <?php foreach ($classes as $class): ?>
        <li>
            <a href="/grades?class_id=<?= $class['id'] ?>">View Grades for <?= htmlspecialchars($class['class_name']) ?></a>
            <a href="/diary?class_id=<?= $class['id'] ?>">View Diary for <?= htmlspecialchars($class['class_name']) ?></a>
        </li>
    <?php endforeach; ?>
</ul>