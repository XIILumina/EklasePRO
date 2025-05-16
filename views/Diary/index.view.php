<?php
// views/grades/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Grades</h1>
        <a href="/grades/create" class="btn btn-primary mb-3">Add New Grade</a>
        <table class="table table-striped">
            <thead>
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
                        <td><?php echo htmlspecialchars($grade['grade_value']); ?></td>
                        <td><?php echo htmlspecialchars($grade['grade_date']); ?></td>
                        <td><?php echo htmlspecialchars($grade['comments'] ?? ''); ?></td>
                        <td>
                            <a href="/grades/edit?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="/grades/delete?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>