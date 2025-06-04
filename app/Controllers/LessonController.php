<?php
namespace App\Controllers;

use App\Models\Lesson;
use App\Models\ClassModel;
use App\Models\User;
use Core\Request;
use Core\Session;
use Core\Mail;

class LessonController extends Controller
{
    protected static string $model = 'Lesson';

    public function index(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('/login');
        }

        try {
            $lessons = Lesson::all()->getAll();
            foreach ($lessons as &$lesson) {
                $assignments = ClassModel::query(
                    "SELECT c.class_name, u.first_name, u.last_name
                     FROM class_lesson_teachers clt
                     JOIN classes c ON clt.class_id = c.id
                     JOIN users u ON clt.teacher_id = u.id
                     WHERE clt.lesson_id = ?",
                    [$lesson['id']]
                )->getAll();
                $lesson['assignments'] = $assignments;
            }
        } catch (\Exception $e) {
            error_log('LessonController: Failed to load lessons: ' . $e->getMessage());
            Session::flash('error', 'Failed to load lessons. Please try again.');
            redirect('/dashboard');
        }

        view('admin/lessons/index', [
            'title' => 'Lessons',
            'lessons' => $lessons,
        ]);
    }

    public function create(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('/login');
        }

        try {
            $classes = ClassModel::all()->getAll();
            $teachers = User::where('role', '=', 'teacher')->getAll();
        } catch (\Exception $e) {
            error_log('LessonController: Failed to load create form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load form data. Please try again.');
            redirect('/lessons');
        }

        view('admin/lessons/create', [
            'title' => 'Create Lesson',
            'classes' => $classes,
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('/login');
        }

        $request->validate([
            'lesson_name' => 'required',
            'description' => 'nullable',
            'class_ids' => 'array',
            'class_ids.*' => 'numeric',
            'teacher_ids' => 'array',
            'teacher_ids.*' => 'numeric',
        ]);

        $data = [
            'lesson_name' => $request->input('lesson_name'),
            'description' => $request->input('description'),
        ];

        try {
            $lesson = Lesson::create($data);
            $lesson_id = $lesson['id']; // Assuming create returns the inserted record

            $class_ids = $request->input('class_ids');
            $teacher_ids = $request->input('teacher_ids');
            foreach ($class_ids as $index => $class_id) {
                if (isset($teacher_ids[$index]) && $teacher_ids[$index]) {
                    ClassModel::query(
                        "INSERT INTO class_lesson_teachers (class_id, lesson_id, teacher_id) VALUES (?, ?, ?)",
                        [(int)$class_id, $lesson_id, (int)$teacher_ids[$index]]
                    )->execute();
                    $teacher = User::find($teacher_ids[$index])->get();
                    $class = ClassModel::find($class_id)->get();
                    $body = "
                        <h2>New Lesson Assignment</h2>
                        <p><strong>Lesson:</strong> {$data['lesson_name']}</p>
                        <p><strong>Class:</strong> {$class['class_name']}</p>
                    ";
                    Mail::send($teacher['email'], 'New Lesson Assignment', $body);
                }
            }

            Session::flash('success', 'Lesson created successfully.');
            redirect('/lessons');
        } catch (\Exception $e) {
            error_log('LessonController: Failed to store lesson: ' . $e->getMessage());
            Session::flash('error', 'Failed to create lesson. Please try again.');
            redirect('/lessons/create');
        }
    }

    public function edit(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('/login');
        }

        $id = (int)$request->input('id');

        try {
            $lesson = Lesson::find($id)->get();
            if (!$lesson) {
                Session::flash('error', 'Lesson not found.');
                redirect('/lessons');
            }
            $classes = ClassModel::all()->getAll();
            $teachers = User::where('role', '=', 'teacher')->getAll();
            $assignments = ClassModel::query(
                "SELECT class_id, teacher_id FROM class_lesson_teachers WHERE lesson_id = ?",
                [$id]
            )->getAll();
        } catch (\Exception $e) {
            error_log('LessonController: Failed to load edit form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load lesson data. Please try again.');
            redirect('/lessons');
        }

        view('admin/lessons/edit', [
            'title' => 'Edit Lesson',
            'lesson' => $lesson,
            'classes' => $classes,
            'teachers' => $teachers,
            'assignments' => $assignments,
        ]);
    }

    public function update(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('/login');
        }

        $request->validate([
            'id' => 'required|numeric',
            'lesson_name' => 'required',
            'description' => 'nullable',
            'class_ids' => 'array',
            'class_ids.*' => 'numeric',
            'teacher_ids' => 'array',
            'teacher_ids.*' => 'numeric',
        ]);

        $id = (int)$request->input('id');
        $data = [
            'lesson_name' => $request->input('lesson_name'),
            'description' => $request->input('description'),
        ];

        try {
            Lesson::update($id, $data);

            // Update class_lesson_teachers
            ClassModel::query("DELETE FROM class_lesson_teachers WHERE lesson_id = ?", [$id])->execute();
            $class_ids = $request->input('class_ids', []);
            $teacher_ids = $request->input('teacher_ids', []);
            foreach ($class_ids as $index => $class_id) {
                if (isset($teacher_ids[$index]) && $teacher_ids[$index]) {
                    ClassModel::query(
                        "INSERT INTO class_lesson_teachers (class_id, lesson_id, teacher_id) VALUES (?, ?, ?)",
                        [(int)$class_id, $id, (int)$teacher_ids[$index]]
                    )->execute();
                    $teacher = User::find($teacher_ids[$index])->get();
                    $class = ClassModel::find($class_id)->get();
                    $body = "
                        <h2>Lesson Assignment Updated</h2>
                        <p><strong>Lesson:</strong> {$data['lesson_name']}</p>
                        <p><strong>Class:</strong> {$class['class_name']}</p>
                    ";
                    Mail::send($teacher['email'], 'Lesson Assignment Updated', $body);
                }
            }

            Session::flash('success', 'Lesson updated successfully.');
            redirect('/lessons');
        } catch (\Exception $e) {
            error_log('LessonController: Failed to update lesson: ' . $e->getMessage());
            Session::flash('error', 'Failed to update lesson. Please try again.');
            redirect('/lessons/' . $id . '/edit');
        }
    }

    public function delete(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('/login');
        }

        $id = (int)$request->input('id');

        try {
            $lesson = Lesson::find($id)->get();
            if (!$lesson) {
                Session::flash('error', 'Lesson not found.');
                redirect('/lessons');
            }

            ClassModel::query("DELETE FROM class_lesson_teachers WHERE lesson_id = ?", [$id])->execute();
            Lesson::delete($id);
            Session::flash('success', 'Lesson deleted successfully.');
            redirect('/lessons');
        } catch (\Exception $e) {
            error_log('LessonController: Failed to delete lesson: ' . $e->getMessage());
            Session::flash('error', 'Failed to delete lesson. Please try again.');
            redirect('/lessons');
        }
    }
}
?>