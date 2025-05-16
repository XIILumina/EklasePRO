<?php
component(component: 'header'); 
?>

<div class="container mt-5">
    <h1 class="mb-4">Grades</h1>
    <?php if (isset($_SESSION['user']['role']) && in_array($_SESSION['user']['role'], ['teacher', 'admin'])): ?>
        <a href="/grades/create" class="btn btn-primary mb-3">Add New Grade</a>
    <?php endif; ?>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Student</th>
                <th>Class</th>
                <th>Lesson</th>
                <th>Grade</th>
                <th>Date</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grades as $grade): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grade['first_name'] . ' ' . $grade['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($grade['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($grade['lesson_name']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($grade['grade_value'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($grade['grade_date']); ?></td>
                    <td><?php echo htmlspecialchars($grade['comments'] ?? ''); ?></td>
                    <?php if (isset($_SESSION['user']['role']) && in_array($_SESSION['user']['role'], ['teacher', 'admin'])): ?>
                        <td>
                            <a href="/grades/<?php echo $grade['id']; ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                            <a href="/grades/<?php echo $grade['id']; ?>/delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this grade?')">Delete</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php component(component: 'footer');  ?>



