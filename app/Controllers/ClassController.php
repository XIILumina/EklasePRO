<?php

namespace App\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Grade;
use Core\Request;
use Core\Session;
use Core\Mail;

class ClassController extends Controller
{
    protected static string $model = 'ClassModel';

    public function index(Request $request): void
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || !isset($_SESSION['user']['id'])) {
            error_log('ClassController: Invalid session data, redirecting to /logout');
            redirect('/logout');
            return;
        }

        $user_role = $_SESSION['user']['role'];
        $user_id = (int)$_SESSION['user']['id'];

        try {
            if ($user_role === 'student') {
                $classes = ClassModel::query(
                    "SELECT c.* FROM classes c 
                     JOIN class_students cs ON c.id = cs.class_id 
                     WHERE cs.user_id = ?",
                    [$user_id]
                )->getAll();
            } else {
                $classes = ClassModel::all()->getAll();
            }

            foreach ($classes as &$class) {
                $class['students'] = User::query(
                    "SELECT u.* FROM users u 
                     JOIN class_students cs ON u.id = cs.user_id 
                     WHERE cs.class_id = ? AND u.role = 'student'",
                    [$class['id']]
                )->getAll();
            }
        } catch (\Exception $e) {
            error_log('ClassController: Failed to load classes: ' . $e->getMessage());
            Session::flash('error', 'Failed to load classes. Please try again.');
            redirect('/dashboard');
            return;
        }

        view('public/classes/index', [
            'title' => 'Classes',
            'classes' => $classes,
        ]);
    }

    public function create(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('ClassController: Unauthorized access to create, redirecting to /login');
            redirect('/login');
            return;
        }

        view('public/classes/create', ['title' => 'Create Class']);
    }

    public function store(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('ClassController: Unauthorized access to store, redirecting to /login');
            redirect('/login');
            return;
        }

        $request->validate([
            'class_name' => 'required|string|max:255',
        ]);

        try {
            ClassModel::create(['class_name' => $request->input('class_name')]);
            Session::flash('success', 'Class created successfully.');
            redirect('/classes');
        } catch (\Exception $e) {
            error_log('ClassController: Failed to create class: ' . $e->getMessage());
            Session::flash('error', 'Failed to create class. Please try again.');
            redirect('/classes/create');
            return;
        }
    }

    public function edit(Request $request, array $parameters = []): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('ClassController: Unauthorized access to edit, redirecting to /login');
            redirect('/login');
            return;
        }

        $id = (int)($parameters['id'] ?? $request->input('id') ?? 0);
        error_log("ClassController: Edit method received id=$id");

        if (!$id) {
            error_log('ClassController: Invalid class ID in edit');
            Session::flash('error', 'Invalid class ID.');
            redirect('/classes');
            return;
        }

        try {
            $class = ClassModel::find($id)->get();
            if (!$class) {
                error_log("ClassController: Class not found for id=$id");
                Session::flash('error', 'Class not found.');
                redirect('/classes');
                return;
            }
        } catch (\Exception $e) {
            error_log('ClassController: Failed to load edit form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load class data. Please try again.');
            redirect('/classes');
            return;
        }

        view('public/classes/edit', [
            'title' => 'Edit Class',
            'class' => $class,
        ]);
    }

    public function update(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('ClassController: Unauthorized access to update, redirecting to /login');
            redirect('/login');
            return;
        }

        $request->validate([
            'id' => 'required|numeric',
            'class_name' => 'required|string|max:255',
        ]);

        $id = (int)$request->input('id');
        error_log("ClassController: Update method received id=$id");

        if (!$id) {
            error_log('ClassController: Invalid class ID in update');
            Session::flash('error', 'Invalid class ID.');
            redirect('/classes');
            return;
        }

        try {
            $class = ClassModel::find($id)->get();
            if (!$class) {
                error_log("ClassController: Class not found for id=$id");
                Session::flash('error', 'Class not found.');
                redirect('/classes');
                return;
            }
            ClassModel::update($id, ['class_name' => $request->input('class_name')]);
            Session::flash('success', 'Class updated successfully.');
            redirect('/classes');
        } catch (\Exception $e) {
            error_log('ClassController: Failed to update class: ' . $e->getMessage());
            Session::flash('error', 'Failed to update class. Please try again.');
            redirect('/classes/' . $id . '/edit');
            return;
        }
    }

    public function delete(Request $request, array $parameters = []): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('ClassController: Unauthorized access to delete, redirecting to /login');
            redirect('/login');
            return;
        }

        $id = (int)($parameters['id'] ?? $request->input('id') ?? 0);
        error_log("ClassController: Delete method received id=$id");

        if (!$id) {
            error_log('ClassController: Invalid class ID in delete');
            Session::flash('error', 'Invalid class ID.');
            redirect('/classes');
            return;
        }

        try {
            $class = ClassModel::find($id)->get();
            if (!$class) {
                error_log("ClassController: Class not found for id=$id");
                Session::flash('error', 'Class not found.');
                redirect('/classes');
                return;
            }

            // Delete related records to avoid foreign key constraints
            ClassModel::query("DELETE FROM class_students WHERE class_id = ?", [$id]);
            ClassModel::query("DELETE FROM class_lesson_teachers WHERE class_id = ?", [$id]);
            ClassModel::query("DELETE FROM grades WHERE class_id = ?", [$id]);

            ClassModel::delete($id);
            Session::flash('success', 'Class deleted successfully.');
            redirect('/classes');
        } catch (\Exception $e) {
            error_log('ClassController: Failed to delete class: ' . $e->getMessage());
            Session::flash('error', 'Failed to delete class: ' . $e->getMessage());
            redirect('/classes');
            return;
        }
    }

  public function addStudents(Request $request, array $parameters = []): void
{
    error_log("ClassController: Entering addStudents method");
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        error_log('ClassController: Unauthorized access to addStudents, redirecting to /login');
        redirect('/login');
        return;
    }

    $class_id = (int)($parameters['id'] ?? $request->input('class_id') ?? 0);
    error_log("ClassController: AddStudents method received class_id=$class_id");

    if (!$class_id) {
        error_log('ClassController: Invalid class ID in addStudents');
        Session::flash('error', 'Invalid class ID.');
        redirect('/classes');
        return;
    }

    try {
        $class = ClassModel::find($class_id)->get();
        if (!$class) {
            error_log("ClassController: Class not found for id=$class_id");
            Session::flash('error', 'Class not found.');
            redirect('/classes');
            return;
        }
        $students = User::query(
            "SELECT u.* FROM users u 
             WHERE u.role = 'student' 
             AND u.id NOT IN (
                 SELECT user_id FROM class_students WHERE class_id = ?
             )",
            [$class_id]
        )->getAll();
        error_log("ClassController: Loaded " . count($students) . " students for class_id=$class_id");

        view('public/classes/add_students', [
            'title' => 'Add Students to ' . htmlspecialchars($class['class_name']),
            'class' => $class,
            'students' => $students,
        ]);
    } catch (\Exception $e) {
        error_log('ClassController: Failed to load add students form: ' . $e->getMessage());
        Session::flash('error', 'Failed to load form data. Please try again.');
        redirect('/classes');
        return;
    }
    }

    public function storeStudents(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('ClassController: Unauthorized access to storeStudents, redirecting to /login');
            redirect('/login');
            return;
        }

        $request->validate([
            'class_id' => 'required|numeric',
            'student_ids' => 'required|array',
            'student_ids.*' => 'numeric',
        ]);

        $class_id = (int)$request->input('class_id');
        $student_ids = $request->input('student_ids');
        error_log("ClassController: StoreStudents method received class_id=$class_id, student_ids=" . implode(',', $student_ids));

        try {
            $class = ClassModel::find($class_id)->get();
            if (!$class) {
                error_log("ClassController: Class not found for id=$class_id");
                throw new \Exception('Class not found.');
            }

            foreach ($student_ids as $student_id) {
                $student = User::find($student_id)->get();
                if ($student && $student['role'] === 'student') {
                    ClassModel::query(
                        "INSERT INTO class_students (class_id, user_id) VALUES (?, ?)",
                        [$class_id, $student_id]
                    );
                    $body = "
                        <h2>Class Assignment</h2>
                        <p>You have been added to the class: <strong>{$class['class_name']}</strong>.</p>
                    ";
                    Mail::send($student['email'], 'Added to Class', $body);
                }
            }

            Session::flash('success', 'Students added successfully.');
            redirect('/classes');
        } catch (\Exception $e) {
            error_log('ClassController: Failed to store students: ' . $e->getMessage());
            Session::flash('error', 'Failed to add students. Please try again.');
            redirect('/classes/' . $class_id . '/add-students');
            return;
        }
        
    }

    public function removeStudent(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('ClassController: Unauthorized access to removeStudent, redirecting to /login');
            redirect('/login');
            return;
        }

        $class_id = (int)($request->input('class_id') ?? 0);
        $student_id = (int)($request->input('student_id') ?? 0);
        error_log("ClassController: RemoveStudent method received class_id=$class_id, student_id=$student_id");

        if (!$class_id || !$student_id) {
            error_log('ClassController: Invalid class or student ID in removeStudent');
            Session::flash('error', 'Invalid class or student ID.');
            redirect('/classes');
            return;
        }

        try {
            $class = ClassModel::find($class_id)->get();
            $student = User::find($student_id)->get();
            if (!$class || !$student || $student['role'] !== 'student') {
                error_log("ClassController: Invalid class or student for class_id=$class_id, student_id=$student_id");
                throw new \Exception('Invalid class or student.');
            }

            ClassModel::query(
                "DELETE FROM class_students WHERE class_id = ? AND user_id = ?",
                [$class_id, $student_id]
            );

            $body = "
                <h2>Class Removal</h2>
                <p>You have been removed from the class: <strong>{$class['class_name']}</strong>.</p>
            ";
            Mail::send($student['email'], 'Removed from Class', $body);

            Session::flash('success', 'Student removed successfully.');
            redirect('/classes');
        } catch (\Exception $e) {
            error_log('ClassController: Failed to remove student: ' . $e->getMessage());
            Session::flash('error', 'Failed to remove student. Please try again.');
            redirect('/classes');
            return;
        }
    }
}