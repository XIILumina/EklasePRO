<?php
// views/grades/edit.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Grade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Grade</h1>
        <form action="/grades/update" method="POST">
            <input type="hidden" name="id" value="<?php echo $grade['id']; ?>">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student</label>
                <select name="student_id" id="student_id" class="form-select" required>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>" <?php echo $student['id'] == $grade['student_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="class_id" class="form-label">Class</label>
                <select name="class_id" id="class_id" class="form-select" required>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo $class['id'] == $grade['class_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="lesson_id" class="form-label">Lesson</label>
                <select name="lesson_id" id="lesson_id" class="form-select" required>
                    <?php foreach ($lessons as $lesson): ?>
                        <option value="<?php echo $lesson['id']; ?>" <?php echo $lesson['id'] == $grade['lesson_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lesson['lesson_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="grade_value" class="form-label">Grade (0-100)</label>
                <input type="number" name="grade_value" id="grade_value" class="form-control" step="0.01" min="0" max="100" value="<?php echo $grade['grade_value']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="grade_date" class="form-label">Date</label>
                <input type="date" name="grade_date" id="grade_date" class="form-control" value="<?php echo $grade['grade_date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="comments" class="form-label">Comments</label>
                <textarea name="comments" id="comments" class="form-control"><?php echo htmlspecialchars($grade['comments'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/grades" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>