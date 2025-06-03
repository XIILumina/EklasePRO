<?php

namespace App\Controllers;

use App\Models\Grade;
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
        $student_id = $request->input('student_id') ? (int)$request->input('student_id') : null;

        $query = "SELECT g.*, u.first_name, u.last_name, l.lesson_name, c.class_name 
                 FROM grades g 
                 JOIN users u ON g.student_id = u.id 
                 JOIN lessons l ON g.lesson_id = l.id 
                 JOIN classes c ON g.class_id = c.id";
        
        $params = [];
        $conditions = [];
        

        if ($user_role === 'student') {
            $conditions[] = "g.student_id = ?";
            $params[] = $user_id;
        } else {
            if ($class_id) {
                $conditions[] = "g.class_id = ?";
                $params[] = $class_id;
            }
            if ($student_id) {
                $conditions[] = "g.student_id = ?";
                $params[] = $student_id;
            }
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        try {
            $grades = Grade::query($query, $params)->getAll();
            $lessonGrades = [];
            $monthsSet = [];

            foreach ($grades as $grade) {
                $lesson = $grade['lesson_name'];
                $month = date('F', strtotime($grade['grade_date'])); // e.g., "January"

                $lessonGrades[$lesson][$month][] = $grade['grade_value'];
                $monthsSet[$month] = true;
            }

            // Sort months
            $sortedMonths = array_keys($monthsSet);
            usort($sortedMonths, function ($a, $b) {
                return date('n', strtotime("1 $a")) - date('n', strtotime("1 $b"));
            });
            $classes = $user_role !== 'student' ? ClassModel::all()->getAll() : [];
        } catch (\Exception $e) {
            error_log('GradeController: Failed to load grades: ' . $e->getMessage());
            Session::flash('error', 'Failed to load grades. Please try again.');
            redirect('/grades');
        }

        view('grades/index', [
            'title' => 'Grades',
            'grades' => $grades,
            'classes' => $classes,
            'class_id' => $class_id,
            'lessonGrades' => $lessonGrades,
            'sortedMonths' => $sortedMonths,
        ]);
    }

    public function create(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $class_id = (int)$request->input('class_id');

        try {
            $classes = ClassModel::all()->getAll();
            $lessons = Lesson::all()->getAll();
            $students = $class_id ? User::query(
                "SELECT u.* FROM users u JOIN class_students cs ON u.id = cs.user_id WHERE cs.class_id = ? AND u.role = 'student'",
                [$class_id]
            )->getAll() : [];
        } catch (\Exception $e) {
            error_log('GradeController: Failed to load create form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load form data. Please try again.');
            redirect('/grades');
        }

        view('grades/create', [
            'title' => 'Add Grade',
            'classes' => $classes,
            'lessons' => $lessons,
            'students' => $students,
            'class_id' => $class_id,
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
            redirect('/grades?class_id=' . $data['class_id']);
        } catch (\Exception $e) {
            error_log('GradeController: Failed to store grade: ' . $e->getMessage());
            Session::flash('error', 'Failed to add grade. Please try again.');
            redirect('/grades/create?class_id=' . $data['class_id']);
        }
    }

    public function edit(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $id = (int)$request->input('id');

        try {
            $grade = Grade::find($id)->get();
            if (!$grade) {
                Session::flash('error', 'Grade not found.');
                redirect('/grades');
            }
            $classes = ClassModel::all()->getAll();
            $lessons = Lesson::all()->getAll();
            $students = User::query(
                "SELECT u.* FROM users u JOIN class_students cs ON u.id = cs.user_id WHERE cs.class_id = ? AND u.role = 'student'",
                [$grade['class_id']]
            )->getAll();
        } catch (\Exception $e) {
            error_log('GradeController: Failed to load edit form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load grade data. Please try again.');
            redirect('/grades');
        }

        view('grades/edit', [
            'title' => 'Edit Grade',
            'grade' => $grade,
            'classes' => $classes,
            'lessons' => $lessons,
            'students' => $students,
        ]);
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
            redirect('/grades?class_id=' . $data['class_id']);
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

        try {
            $grade = Grade::find($id)->get();
            if (!$grade) {
                Session::flash('error', 'Grade not found.');
                redirect('/grades');
            }

            Grade::delete($id);
            Session::flash('success', 'Grade deleted successfully.');
            redirect('/grades?class_id=' . $grade['class_id']);
        } catch (\Exception $e) {
            error_log('GradeController: Failed to delete grade: ' . $e->getMessage());
            Session::flash('error', 'Failed to delete grade. Please try again.');
            redirect('/grades');
        }
    }

    public function bulkUpdate(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('/login');
        }

        $request->validate([
            'grades' => 'required|array',
            'grades.*.id' => 'required|numeric',
            'grades.*.grade_value' => 'required|numeric|min:0|max:100',
            'grades.*.grade_date' => 'required|date',
            'grades.*.comments' => 'nullable',
        ]);

        $grades = $request->input('grades');
        $class_id = (int)$request->input('class_id');

        try {
            foreach ($grades as $gradeData) {
                $data = [
                    'student_id' => (int)$gradeData['student_id'],
                    'class_id' => (int)$gradeData['class_id'],
                    'lesson_id' => (int)$gradeData['lesson_id'],
                    'teacher_id' => (int)$_SESSION['user']['id'],
                    'grade_value' => (float)$gradeData['grade_value'],
                    'grade_date' => $gradeData['grade_date'],
                    'comments' => $gradeData['comments'] ?? null,
                ];
                Grade::update((int)$gradeData['id'], $data);
            }
            Session::flash('success', 'Grades updated successfully.');
            redirect('/grades?class_id=' . $class_id);
        } catch (\Exception $e) {
            error_log('GradeController: Failed to bulk update grades: ' . $e->getMessage());
            Session::flash('error', 'Failed to update grades. Please try again.');
            redirect('/grades?class_id=' . $class_id);
        }
    }
}