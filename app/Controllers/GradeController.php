<?php

namespace App\Controllers;

use App\Models\Grade;
use Core\Request;
use Core\Session;

class GradeController extends Controller
{
    protected static string $model = 'Grade';

    // Display all grades for a class, student, or the logged-in student
    public function index(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $user_role = $_SESSION['user']['role'] ?? null;
        $user_id = (int)$_SESSION['user']['id'];

        // Debug: Log role to verify it's correct
        // Uncomment to debug
        // error_log("User role: $user_role, User ID: $user_id");

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
            // Students can only view their own grades
            $conditions[] = "g.student_id = ?";
            $params[] = $user_id;
        } else if (!in_array($user_role, ['teacher', 'admin'])) {
            // Redirect non-authorized users (shouldn't reach here, but as a fallback)
            Session::flash('error', 'Unauthorized access.');
            redirect('/login');
        } else {
            // Teachers/admins can filter by class_id or student_id
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
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to load grades. Please try again.');
            redirect('/grades');
        }

        view('grades/index', ['title' => 'Grades', 'grades' => $grades]);
    }

    // Show form to create a new grade
    public function create(): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        try {
            $classes = Grade::query("SELECT * FROM classes")->getAll();
            $lessons = Grade::query("SELECT * FROM lessons")->getAll();
            $students = Grade::query("SELECT u.* FROM users u JOIN class_students cs ON u.id = cs.user_id WHERE u.role = 'student'")->getAll();
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to load form data. Please try again.');
            redirect('/grades');
        }

        view('grades/create', [
            'title' => 'Add Grade',
            'classes' => $classes,
            'lessons' => $lessons,
            'students' => $students
        ]);
    }

    // Store a new grade
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
            'comments' => 'nullable'
        ]);

        $data = [
            'student_id' => (int)$request->input('student_id'),
            'class_id' => (int)$request->input('class_id'),
            'lesson_id' => (int)$request->input('lesson_id'),
            'teacher_id' => (int)$_SESSION['user']['id'],
            'grade_value' => (float)$request->input('grade_value'),
            'grade_date' => $request->input('grade_date'),
            'comments' => $request->input('comments')
        ];

        try {
            Grade::create($data);
            Session::flash('success', 'Grade added successfully.');
            redirect_and_save('/grades?class_id=' . $data['class_id'], [], $data, 'Grade', 'store');
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to add grade. Please try again.');
            redirect('/grades/create');
        }
    }

    // Show form to edit a grade
    public function edit(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $id = (int)$request->input('id');

        try {
            $grade = Grade::where('id', '=', $id)->get();
            $classes = Grade::query("SELECT * FROM classes")->getAll();
            $lessons = Grade::query("SELECT * FROM lessons")->getAll();
            $students = Grade::query("SELECT u.* FROM users u JOIN class_students cs ON u.id = cs.user_id WHERE u.role = 'student'")->getAll();
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to load grade data. Please try again.');
            redirect('/grades');
        }

        if (!$grade) {
            Session::flash('error', 'Grade not found.');
            redirect('/grades');
        }

        view('grades/edit', [
            'title' => 'Edit Grade',
            'grade' => $grade,
            'classes' => $classes,
            'lessons' => $lessons,
            'students' => $students
        ]);
    }

    // Update a grade
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
            'comments' => 'nullable'
        ]);

        $id = (int)$request->input('id');
        $data = [
            'student_id' => (int)$request->input('student_id'),
            'class_id' => (int)$request->input('class_id'),
            'lesson_id' => (int)$request->input('lesson_id'),
            'grade_value' => (float)$request->input('grade_value'),
            'grade_date' => $request->input('grade_date'),
            'comments' => $request->input('comments')
        ];

        try {
            Grade::update($id, $data);
            Session::flash('success', 'Grade updated successfully.');
            redirect_and_save('/grades?class_id=' . $data['class_id'], [], $data, 'Grade', 'update');
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to update grade. Please try again.');
            redirect('/grades/' . $id . '/edit');
        }
    }

    // Delete a grade
    public function delete(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $id = (int)$request->input('id');

        try {
            $grade = Grade::where('id', '=', $id)->get();
            if (!$grade) {
                Session::flash('error', 'Grade not found.');
                redirect('/grades');
            }

            Grade::delete($id);
            Session::flash('success', 'Grade deleted successfully.');
            redirect_and_save('/grades', [], $id, 'Grade', 'destroy');
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to delete grade. Please try again.');
            redirect('/grades');
        }
    }
}