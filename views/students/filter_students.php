<form action="/filter-students" method="GET">
    <label for="filter" class="block">Search students by name or surname:</label>
    <input type="text" name="filter" id="filter" placeholder="Enter name or surname">
    <button type="submit" class="btn btn-primary">Search</button>
</form>

<div class="students-list">
    <?php
    if (!empty($students)) {
        foreach ($students as $student) {
            echo "<p>" . htmlspecialchars($student->first_name) . " " . htmlspecialchars($student->last_name) . "</p>";
        }
    }
    ?>
</div>
