<?php
namespace App\Controllers;

use App\Models\Grade;
use App\Models\Detention;
use App\Models\ClassModel;
use App\Models\Lesson;
use App\Models\User;
use Core\Request;
use Core\Session;
use Core\Mail;

class GradeController extends Controller
{
    protected static string $model = 'Grade';

    public function index(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $user_role = $_SESSION['user']['role'];
        $user_id = (int)$_SESSION['user']['id'];
        $class_id = $request->input('class_id') ? (int)$request->input('class_id') : null;
        $lesson_id = $request->input('lesson_id') ? (int)$request->input('lesson_id') : null;
        $week_start = $request->input('week') ? $request->input('week') : date('Y-m-d', strtotime('monday this week'));

        if ($user_role === 'student') {
            // Student view (unchanged)
            $query = "
                SELECT l.lesson_name, g.id AS grade_id, g.grade_value, g.grade_date, u.first_name, u.last_name, c.class_name
                FROM lessons l
                JOIN class_lesson_teachers clt ON l.id = clt.lesson_id
                JOIN classes c ON clt.class_id = c.id
                LEFT JOIN grades g ON l.id = g.lesson_id AND g.class_id = clt.class_id AND g.student_id = ?
                JOIN class_students cs ON cs.class_id = clt.class_id
                WHERE cs.user_id = ?
            ";
            $params = [$user_id, $user_id];
            $conditions = [];

            try {
                $grades = Grade::query($query, $params)->getAll();
                $lessonGrades = [];
                $monthsSet = [];

                $classLessons = Lesson::query(
                    "SELECT DISTINCT l.lesson_name
                     FROM lessons l
                     JOIN class_lesson_teachers clt ON l.id = clt.lesson_id
                     JOIN class_students cs ON cs.class_id = clt.class_id
                     WHERE cs.user_id = ?",
                    [$user_id]
                )->getAll();

                foreach ($classLessons as $lesson) {
                    $lessonGrades[$lesson['lesson_name']] = array_fill_keys(
                        ['September', 'October', 'November', 'December', 'January', 'February', 'March', 'April', 'May', 'June'],
                        []
                    );
                }

                foreach ($grades as $grade) {
                    $lesson = $grade['lesson_name'];
                    if ($grade['grade_date']) {
                        $month = date('F', strtotime($grade['grade_date']));
                        $lessonGrades[$lesson][$month][] = $grade['grade_value'];
                        $monthsSet[$month] = true;
                    }
                }

                $detentions = Detention::query(
                    "SELECT d.*, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name 
                     FROM detentions d 
                     JOIN users t ON d.teacher_id = t.id 
                     WHERE d.student_id = ?",
                    [$user_id]
                )->getAll();

                $lessonGrades['Detention'] = array_fill_keys(
                    ['September', 'October', 'November', 'December', 'January', 'February', 'March', 'April', 'May', 'June'],
                    []
                );
                foreach ($detentions as $detention) {
                    $month = date('F', strtotime($detention['detention_date']));
                    $lessonGrades['Detention'][$month][] = $detention['reason'];
                }

                $sortedMonths = ['September', 'October', 'November', 'December', 'January', 'February', 'March', 'April', 'May', 'June'];
                $classes = [];

                view('student/grades/index', [
                    'title' => 'Grades',
                    'grades' => $grades,
                    'classes' => $classes,
                    'class_id' => $class_id,
                    'lessonGrades' => $lessonGrades,
                    'sortedMonths' => $sortedMonths,
                    'user_role' => $user_role,
                    'detentions' => $detentions,
                    'student_id' => $user_id,
                ]);
            } catch (\Exception $e) {
                error_log('GradeController: Failed to load grades: ' . $e->getMessage());
                Session::flash('error', 'Failed to load grades. Please try again.');
                redirect('/grades');
            }
        } else {
            // Teacher or Admin view
            if (!$class_id) {
                try {
                    $classes = ClassModel::query(
                        $user_role === 'teacher' ?
                            "SELECT DISTINCT c.* FROM classes c JOIN class_lesson_teachers clt ON c.id = clt.class_id WHERE clt.teacher_id = ?" :
                            "SELECT * FROM classes",
                        $user_role === 'teacher' ? [$user_id] : []
                    )->getAll();
                } catch (\Exception $e) {
                    error_log('GradeController: Failed to load classes: ' . $e->getMessage());
                    Session::flash('error', 'Failed to load classes. Please try again.');
                    redirect('/grades');
                }
                view($user_role . '/grades/select_class', [
                    'title' => 'Select Class',
                    'classes' => $classes,
                ]);
                return;
            }

            if (!$lesson_id) {
                try {
                    $lessons = Lesson::query(
                        $user_role === 'teacher' ?
                            "SELECT DISTINCT l.* FROM lessons l JOIN class_lesson_teachers clt ON l.id = clt.lesson_id WHERE clt.class_id = ? AND clt.teacher_id = ?" :
                            "SELECT DISTINCT l.* FROM lessons l JOIN class_lesson_teachers clt ON l.id = clt.lesson_id WHERE clt.class_id = ?",
                        $user_role === 'teacher' ? [$class_id, $user_id] : [$class_id]
                    )->getAll();
                } catch (\Exception $e) {
                    error_log('GradeController: Failed to load lessons: ' . $e->getMessage());
                    Session::flash('error', 'Failed to load lessons. Please try again.');
                    redirect('/grades?class_id=' . $class_id);
                }
                view($user_role . '/grades/select_lesson', [
                    'title' => 'Select Lesson',
                    'lessons' => $lessons,
                    'class_id' => $class_id,
                ]);
                return;
            }

            try {
                // Fetch students and grades for the selected class and lesson
                $query = "
                    SELECT u.id AS student_id, u.first_name, u.last_name, g.id AS grade_id, g.grade_value, g.grade_date
                    FROM users u
                    JOIN class_students cs ON u.id = cs.user_id
                    LEFT JOIN grades g ON u.id = g.student_id AND g.class_id = ? AND g.lesson_id = ?
                    WHERE cs.class_id = ? AND u.role = 'student'
                    ORDER BY u.last_name, u.first_name
                ";
                $params = [$class_id, $lesson_id, $class_id];
                $grades = Grade::query($query, $params)->getAll();

                $students = [];
                $gradeMatrix = [];
                $weekDays = [];
                $startDate = new \DateTime($week_start);
                for ($i = 0; $i < 5; $i++) { // Monday to Friday
                    $weekDays[] = $startDate->format('Y-m-d l');
                    $startDate->modify('+1 day');
                }

                foreach ($grades as $grade) {
                    $student_id = $grade['student_id'];
                    if (!isset($students[$student_id])) {
                        $students[$student_id] = [
                            'first_name' => $grade['first_name'],
                            'last_name' => $grade['last_name'],
                        ];
                    }
                    if ($grade['grade_date']) {
                        $gradeDate = date('Y-m-d', strtotime($grade['grade_date']));
                        if (in_array($gradeDate . ' ' . date('l', strtotime($gradeDate)), $weekDays)) {
                            $gradeMatrix[$student_id][$gradeDate] = [
                                'id' => $grade['grade_id'],
                                'value' => $grade['grade_value'],
                            ];
                        }
                    }
                }

                $classes = ClassModel::query(
                    $user_role === 'teacher' ?
                        "SELECT DISTINCT c.* FROM classes c JOIN class_lesson_teachers clt ON c.id = clt.class_id WHERE clt.teacher_id = ?" :
                        "SELECT * FROM classes",
                    $user_role === 'teacher' ? [$user_id] : []
                )->getAll();

                $lessons = Lesson::query(
                    $user_role === 'teacher' ?
                        "SELECT DISTINCT l.* FROM lessons l JOIN class_lesson_teachers clt ON l.id = clt.lesson_id WHERE clt.class_id = ? AND clt.teacher_id = ?" :
                        "SELECT DISTINCT l.* FROM lessons l JOIN class_lesson_teachers clt ON l.id = clt.lesson_id WHERE clt.class_id = ?",
                    $user_role === 'teacher' ? [$class_id, $user_id] : [$class_id]
                )->getAll();

                // Calculate previous and next week
                $prevWeek = (new \DateTime($week_start))->modify('-1 week')->format('Y-m-d');
                $nextWeek = (new \DateTime($week_start))->modify('+1 week')->format('Y-m-d');

                view($user_role . '/grades/index', [
                    'title' => 'Grade Matrix',
                    'classes' => $classes,
                    'class_id' => $class_id,
                    'lessons' => $lessons,
                    'lesson_id' => $lesson_id,
                    'students' => $students,
                    'gradeMatrix' => $gradeMatrix,
                    'weekDays' => $weekDays,
                    'week_start' => $week_start,
                    'prev_week' => $prevWeek,
                    'next_week' => $nextWeek,
                    'user_role' => $user_role,
                ]);
            } catch (\Exception $e) {
                error_log('GradeController: Failed to load grade matrix: ' . $e->getMessage());
                Session::flash('error', 'Failed to load grade matrix. Please try again.');
                redirect('/grades?class_id=' . $class_id);
            }
        }
    }

    public function create(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $class_id = (int)$request->input('class_id');
        $lesson_id = (int)$request->input('lesson_id');

        try {
            $classes = ClassModel::all()->getAll();
            $lessons = $lesson_id ? Lesson::find($lesson_id)->getAll() : Lesson::all()->getAll();
            $students = $class_id ? User::query(
                "SELECT u.* FROM users u JOIN class_students cs ON u.id = cs.user_id WHERE cs.class_id = ? AND u.role = 'student'",
                [$class_id]
            )->getAll() : [];
        } catch (\Exception $e) {
            error_log('GradeController: Failed to load create form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load form data. Please try again.');
            redirect('/grades');
        }

        view($_SESSION['user']['role'] . '/grades/create', [
            'title' => 'Add Grade',
            'classes' => $classes,
            'lessons' => $lessons,
            'students' => $students,
            'class_id' => $class_id,
            'lesson_id' => $lesson_id,
        ]);
    }

    public function store(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $request->validate([
            'student_id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'lesson_id' => 'required|numeric',
            'grade_value' => 'required|numeric|min:0|max:100',
            'grade_date' => 'required|date',
            'comments' => 'nullable',
        ]);

        $user_id = (int)$_SESSION['user']['id'];
        $data = [
            'student_id' => (int)$request->input('student_id'),
            'class_id' => (int)$request->input('class_id'),
            'lesson_id' => (int)$request->input('lesson_id'),
            'teacher_id' => $user_id,
            'grade_value' => (float)$request->input('grade_value'),
            'grade_date' => $request->input('grade_date'),
            'comments' => $request->input('comments'),
        ];

        if ($_SESSION['user']['role'] === 'teacher') {
            $assignment = ClassModel::query(
                "SELECT * FROM class_lesson_teachers WHERE class_id = ? AND lesson_id = ? AND teacher_id = ?",
                [$data['class_id'], $data['lesson_id'], $user_id]
            )->get();
            if (!$assignment) {
                Session::flash('error', 'You are not assigned to teach this lesson in this class.');
                redirect('/grades?class_id=' . $data['class_id']);
            }
        }

        try {
            Grade::create($data);

            $student = User::find($data['student_id'])->get();
            $lesson = Lesson::find($data['lesson_id'])->get();
            $class = ClassModel::find($data['class_id'])->get();
            $body = "
                <h2>New Grade Assigned</h2>
                <p><strong>Class:</strong> {$class['class_name']}</p>
                <p><strong>Lesson:</strong> {$lesson['lesson_name']}</p>
                <p><strong>Grade:</strong> {$data['grade_value']}</p>
                <p><strong>Date:</strong> {$data['grade_date']}</p>
                <p><strong>Comments:</strong> " . ($data['comments'] ?: 'None') . "</p>
            ";
            Mail::send($student['email'], 'New Grade Assigned', $body);

            Session::flash('success', 'Grade added successfully.');
            redirect('/grades?class_id=' . $data['class_id'] . '&lesson_id=' . $data['lesson_id']);
        } catch (\Exception $e) {
            error_log('GradeController: Failed to store grade: ' . $e->getMessage());
            Session::flash('error', 'Failed to add grade. Please try again.');
            redirect('/grades/create?class_id=' . $data['class_id'] . '&lesson_id=' . $data['lesson_id']);
        }
    }

    public function edit(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $class_id = (int)$request->input('class_id');
        $lesson_id = (int)$request->input('lesson_id');
        $week_start = $request->input('week') ? $request->input('week') : date('Y-m-d', strtotime('monday this week'));

        try {
            // Fetch students and grades for the selected class and lesson
            $query = "
                SELECT u.id AS student_id, u.first_name, u.last_name, g.id AS grade_id, g.grade_value, g.grade_date
                FROM users u
                JOIN class_students cs ON u.id = cs.user_id
                LEFT JOIN grades g ON u.id = g.student_id AND g.class_id = ? AND g.lesson_id = ?
                WHERE cs.class_id = ? AND u.role = 'student'
                ORDER BY u.last_name, u.first_name
            ";
            $params = [$class_id, $lesson_id, $class_id];
            $grades = Grade::query($query, $params)->getAll();

            $students = [];
            $gradeMatrix = [];
            $weekDays = [];
            $startDate = new \DateTime($week_start);
            for ($i = 0; $i < 5; $i++) { // Monday to Friday
                $weekDays[] = $startDate->format('Y-m-d l');
                $startDate->modify('+1 day');
            }

            foreach ($grades as $grade) {
                $student_id = $grade['student_id'];
                if (!isset($students[$student_id])) {
                    $students[$student_id] = [
                        'first_name' => $grade['first_name'],
                        'last_name' => $grade['last_name'],
                    ];
                }
                if ($grade['grade_date']) {
                    $gradeDate = date('Y-m-d', strtotime($grade['grade_date']));
                    if (in_array($gradeDate . ' ' . date('l', strtotime($gradeDate)), $weekDays)) {
                        $gradeMatrix[$student_id][$gradeDate] = [
                            'id' => $grade['grade_id'],
                            'value' => $grade['grade_value'],
                        ];
                    }
                }
            }
            $user_role = $_SESSION['user']['role'];
            $user_id = (int)$_SESSION['user']['id'];
            $classes = ClassModel::query(
                $user_role === 'teacher' ?
                    "SELECT DISTINCT c.* FROM classes c JOIN class_lesson_teachers clt ON c.id = clt.class_id WHERE clt.teacher_id = ?" :
                    "SELECT * FROM classes",
                $user_role === 'teacher' ? [$user_id] : []
            )->getAll();

            $lessons = Lesson::query(
                $user_role === 'teacher' ?
                    "SELECT DISTINCT l.* FROM lessons l JOIN class_lesson_teachers clt ON l.id = clt.lesson_id WHERE clt.class_id = ? AND clt.teacher_id = ?" :
                    "SELECT DISTINCT l.* FROM lessons l JOIN class_lesson_teachers clt ON l.id = clt.lesson_id WHERE clt.class_id = ?",
                $user_role === 'teacher' ? [$class_id, $user_id] : [$class_id]
            )->getAll();

            // Calculate previous and next week
            $prevWeek = (new \DateTime($week_start))->modify('-1 week')->format('Y-m-d');
            $nextWeek = (new \DateTime($week_start))->modify('+1 week')->format('Y-m-d');

            view($_SESSION['user']['role'] . '/grades/edit', [
                'title' => 'Edit Grade Matrix',
                'classes' => $classes,
                'class_id' => $class_id,
                'lessons' => $lessons,
                'lesson_id' => $lesson_id,
                'students' => $students,
                'gradeMatrix' => $gradeMatrix,
                'weekDays' => $weekDays,
                'week_start' => $week_start,
                'prev_week' => $prevWeek,
                'next_week' => $nextWeek,
                'user_role' => $user_role,
            ]);
        } catch (\Exception $e) {
            error_log('GradeController: Failed to load edit matrix: ' . $e->getMessage());
            Session::flash('error', 'Failed to load grade matrix. Please try again.');
            redirect('/grades?class_id=' . $class_id);
        }
    }

    public function update(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $request->validate([
            'id' => 'required|numeric',
            'student_id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'lesson_id' => 'required|numeric',
            'grade_value' => 'required|numeric|min:0|max:100',
            'grade_date' => 'required|date',
            'comments' => 'nullable',
        ]);

        $id = (int)$request->input('id');
        $data = [
            'student_id' => (int)$request->input('student_id'),
            'class_id' => (int)$request->input('class_id'),
            'lesson_id' => (int)$request->input('lesson_id'),
            'teacher_id' => (int)$_SESSION['user']['id'],
            'grade_value' => (float)$request->input('grade_value'),
            'grade_date' => $request->input('grade_date'),
            'comments' => $request->input('comments'),
        ];

        try {
            if ($_SESSION['user']['role'] === 'teacher') {
                $grade = Grade::find($id)->get();
                if (!$grade || $grade['teacher_id'] != $_SESSION['user']['id']) {
                    Session::flash('error', 'You are not authorized to edit this grade.');
                    redirect('/grades?class_id=' . $data['class_id']);
                }
                $assignment = ClassModel::query(
                    "SELECT * FROM class_lesson_teachers WHERE class_id = ? AND lesson_id = ? AND teacher_id = ?",
                    [$data['class_id'], $data['lesson_id'], $_SESSION['user']['id']]
                )->get();
                if (!$assignment) {
                    Session::flash('error', 'You are not assigned to teach this lesson in this class.');
                    redirect('/grades?class_id=' . $data['class_id']);
                }
            }

            Grade::update($id, $data);

            $student = User::find($data['student_id'])->get();
            $lesson = Lesson::find($data['lesson_id'])->get();
            $class = ClassModel::find($data['class_id'])->get();
            $body = "
                <h2>Grade Updated</h2>
                <p><strong>Class:</strong> {$class['class_name']}</p>
                <p><strong>Lesson:</strong> {$lesson['lesson_name']}</p>
                <p><strong>Grade:</strong> {$data['grade_value']}</p>
                <p><strong>Date:</strong> {$data['grade_date']}</p>
                <p><strong>Comments:</strong> " . ($data['comments'] ?: 'None') . "</p>
            ";
            Mail::send($student['email'], 'Grade Updated', $body);

            Session::flash('success', 'Grade updated successfully.');
            redirect('/grades?class_id=' . $data['class_id'] . '&lesson_id=' . $data['lesson_id']);
        } catch (\Exception $e) {
            error_log('GradeController: Failed to update grade: ' . $e->getMessage());
            Session::flash('error', 'Failed to update grade. Please try again.');
            redirect('/grades/' . $id . '/edit');
        }
    }

    public function delete(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $id = (int)$request->input('id');
        $class_id = (int)$request->input('class_id');
        $lesson_id = (int)$request->input('lesson_id');

        try {
            $grade = Grade::find($id)->get();
            if (!$grade) {
                Session::flash('error', 'Grade not found.');
                redirect('/grades?class_id=' . $class_id);
            }
            if ($_SESSION['user']['role'] === 'teacher' && $grade['teacher_id'] != $_SESSION['user']['id']) {
                Session::flash('error', 'You are not authorized to delete this grade.');
                redirect('/grades?class_id=' . $class_id);
            }

            Grade::delete($id);
            Session::flash('success', 'Grade deleted successfully.');
            redirect('/grades?class_id=' . $class_id . '&lesson_id=' . $lesson_id);
        } catch (\Exception $e) {
            error_log('GradeController: Failed to delete grade: ' . $e->getMessage());
            Session::flash('error', 'Failed to delete grade. Please try again.');
            redirect('/grades?class_id=' . $class_id);
        }
    }

    public function bulkUpdate(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|numeric',
            'grades.*.grade_value' => 'nullable|numeric|min:0|max:100',
            'class_id' => 'required|numeric',
            'lesson_id' => 'required|numeric',
            'grades.*.grade_date' => 'required|date',
            'grades.*.comments' => 'nullable',
        ]);

        $grades = $request->input('grades');
        $class_id = (int)$request->input('class_id');
        $lesson_id = (int)$request->input('lesson_id');
        $user_id = (int)$_SESSION['user']['id'];

        try {
            if ($_SESSION['user']['role'] === 'teacher') {
                $assignment = ClassModel::query(
                    "SELECT * FROM class_lesson_teachers WHERE class_id = ? AND lesson_id = ? AND teacher_id = ?",
                    [$class_id, $lesson_id, $user_id]
                )->get();
                if (!$assignment) {
                    Session::flash('error', 'You are not assigned to teach this lesson in this class.');
                    redirect('/grades?class_id=' . $class_id);
                }
            }

            foreach ($grades as $gradeData) {
                // Skip if grade_value is empty or not set
                if (!isset($gradeData['grade_value']) || trim($gradeData['grade_value']) === '') {
                    if (isset($gradeData['id']) && $gradeData['id']) {
                        // Delete existing grade if it exists
                        $grade_id = (int)$gradeData['id'];
                        if ($_SESSION['user']['role'] === 'teacher') {
                            $existing_grade = Grade::find($grade_id)->get();
                            if (!$existing_grade || $existing_grade['teacher_id'] != $user_id) {
                                continue;
                            }
                        }
                        Grade::delete($grade_id);
                    }
                    continue;
                }

                $data = [
                    'student_id' => (int)$gradeData['student_id'],
                    'class_id' => $class_id,
                    'lesson_id' => $lesson_id,
                    'teacher_id' => $user_id,
                    'grade_value' => (float)$gradeData['grade_value'],
                    'grade_date' => $gradeData['grade_date'],
                    'comments' => $gradeData['comments'] ?? null,
                ];

                if (isset($gradeData['id']) && $gradeData['id']) {
                    $grade_id = (int)$gradeData['id'];
                    if ($_SESSION['user']['role'] === 'teacher') {
                        $existing_grade = Grade::find($grade_id)->get();
                        if (!$existing_grade || $existing_grade['teacher_id'] != $user_id) {
                            continue;
                        }
                    }
                    Grade::update($grade_id, $data);
                } else {
                    Grade::create($data);
                }

                $student = User::find($data['student_id'])->get();
                $lesson = Lesson::find($data['lesson_id'])->get();
                $class = ClassModel::find($data['class_id'])->get();
                $body = "
                    <h2>" . (isset($gradeData['id']) ? 'Grade Updated' : 'New Grade Assigned') . "</h2>
                    <p><strong>Class:</strong> {$class['class_name']}</p>
                    <p><strong>Lesson:</strong> {$lesson['lesson_name']}</p>
                    <p><strong>Grade:</strong> {$data['grade_value']}</p>
                    <p><strong>Date:</strong> {$data['grade_date']}</p>
                    <p><strong>Comments:</strong> " . ($data['comments'] ?: 'None') . "</p>
                ";
                Mail::send($student['email'], isset($gradeData['id']) ? 'Grade Updated' : 'New Grade Assigned', $body);
            }
            Session::flash('success', 'Grades updated successfully.');
            redirect('/grades?class_id=' . $class_id . '&lesson_id=' . $lesson_id);
        } catch (\Exception $e) {
            error_log('GradeController: Failed to bulk update grades: ' . $e->getMessage());
            Session::flash('error', 'Failed to update grades. Please try again.');
            redirect('/grades?class_id=' . $class_id . '&lesson_id=' . $lesson_id);
        }
    }

    public function createDetention(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $class_id = (int)$request->input('class_id');
        $student_id = (int)$request->input('student_id');

        try {
            $classes = ClassModel::all()->getAll();
            $students = $class_id ? User::query(
                "SELECT u.* FROM users u JOIN class_students cs ON u.id = cs.user_id WHERE cs.class_id = ? AND u.role = 'student'",
                [$class_id]
            )->getAll() : [];
        } catch (\Exception $e) {
            error_log('GradeController: Failed to load detention form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load form data. Please try again.');
            redirect('/grades?class_id=' . $class_id);
        }

        view($_SESSION['user']['role'] . '/detentions/create', [
            'title' => 'Add Detention',
            'classes' => $classes,
            'students' => $students,
            'class_id' => $class_id,
            'student_id' => $student_id,
        ]);
    }

    public function storeDetention(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $request->validate([
            'student_id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'reason' => 'required',
            'detention_date' => 'required|date',
        ]);

        $user_id = (int)$_SESSION['user']['id'];
        $data = [
            'student_id' => (int)$request->input('student_id'),
            'teacher_id' => $user_id,
            'reason' => $request->input('reason'),
            'detention_date' => $request->input('detention_date'),
        ];

        try {
            Detention::create($data);

            $student = User::find($data['student_id'])->get();
            $class = ClassModel::find($request->input('class_id'))->get();
            $body = "
                <h2>New Detention Assigned</h2>
                <p><strong>Student:</strong> {$student['first_name']} {$student['last_name']}</p>
                <p><strong>Class:</strong> {$class['class_name']}</p>
                <p><strong>Reason:</strong> {$data['reason']}</p>
                <p><strong>Date:</strong> {$data['detention_date']}</p>
            ";
            Mail::send($student['email'], 'New Detention Assigned', $body);

            Session::flash('success', 'Detention added successfully.');
            redirect('/grades?class_id=' . $request->input('class_id'));
        } catch (\Exception $e) {
            error_log('GradeController: Failed to store detention: ' . $e->getMessage());
            Session::flash('error', 'Failed to add detention. Please try again.');
            redirect('/detentions/create?class_id=' . $request->input('class_id'));
        }
    }
}
?>