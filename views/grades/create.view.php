<?php
// views/grades/create.view.php
require base_path('views/layouts/app.view.php');
?>

<div class="container mt-5">
    <h1 class="mb-4">Add New Grade</h1>


    <form action="/grades/store" method="POST">
        <div class="mb-3">
            <label for="student_id" class="form-label">Student</label>
            <select name="student_id" id="student_id" class="form-select" required>
                <option value="">Select a student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>" <?php echo old('student_id') == $student['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php echo error('student_id'); ?>
        </div>
        <div class="mb-3">
            <label for="class_id" class="form-label">Class</label>
            <select name="class_id" id="class_id" class="form-select" required>
                <option value="">Select a class</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>" <?php echo old('class_id') == $class['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($class['class_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php echo error('class_id'); ?>
        </div>
        <div class="mb-3">
            <label for="lesson_id" class="form-label">Lesson</label>
            <select name="lesson_id" id="lesson_id" class="form-select" required>
                <option value="">Select a lesson વi></option>
                <?php foreach ($lessons as $lesson): ?>
                    <option value="<?php echo $lesson['id']; ?>" <?php echo old('lesson_id') == $lesson['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($lesson['lesson_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php echo error('lesson_id'); ?>
        </div>
        <div class="mb-3">
            <label for="grade_value" class="form-label">Grade (0-100)</label>
            <input type="number" name="grade_value" id="grade_value" class="form-control" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars(old('grade_value')); ?>" required>
            <?php echo error('grade_value'); ?>
        </div>
        <div class="mb-3">
            <label for="grade_date" class="form-label">Date</label>
            <input type="date" name="grade_date" id="grade_date" class="form-control" value="<?php echo htmlspecialchars(old('grade_date')); ?>" required>
            <?php echo error('grade_date'); ?>
        </div>
        <div class="mb-3">
            <label for="comments" class="form-label">Comments</label>
            <textarea name="comments" id="comments" class="form-control"><?php echo htmlspecialchars(old('comments')); ?></textarea>
            <?php echo error('comments'); ?>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="/grades" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require base_path('views/layouts/footer.view.php'); ?>