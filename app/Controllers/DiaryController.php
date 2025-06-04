<?php
namespace App\Controllers;

use App\Models\Diary;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Lesson;
use Core\Request;
use Core\Session;
use Core\Mail;

class DiaryController extends Controller
{
    protected static string $model = 'Diary';

    public function index(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            error_log('DiaryController: No user session, redirecting to /login');
            redirect('/login');
        }

        $user_role = $_SESSION['user']['role'];
        $user_id = (int)$_SESSION['user']['id'];
        $class_id = $request->input('class_id') ? (int)$request->input('class_id') : null;
        $start_date = $request->input('week') ?: date('Y-m-d');

        if ($user_role === 'student') {
            $class = ClassModel::query(
                "SELECT class_id FROM class_students WHERE user_id = ?",
                [$user_id]
            )->get();
            $class_id = $class ? $class['class_id'] : null;
            if (!$class_id) {
                error_log("DiaryController: Student ID $user_id has no class assigned");
                redirect('/dashboard');
            }
        } elseif (!$class_id) {
            try {
                $classes = ClassModel::query(
                    $user_role === 'teacher' ?
                        "SELECT DISTINCT c.* FROM classes c JOIN class_lesson_teachers clt ON c.id = clt.class_id WHERE clt.teacher_id = ?" :
                        "SELECT * FROM classes",
                    $user_role === 'teacher' ? [$user_id] : []
                )->getAll();
            } catch (\Exception $e) {
                error_log('DiaryController: Failed to load classes: ' . $e->getMessage());
                Session::flash('error', 'Failed to load classes. Please try again.');
                redirect('/dashboard');
            }
            view($user_role . '/diaries/select_class', [
                'title' => 'Select Class',
                'classes' => $classes,
            ]);
            return;
        }

        try {
            $diary = Diary::getWeekDiary($class_id, $start_date);
            $time_slots = Diary::getTimeSlots();
        } catch (\Exception $e) {
            error_log('DiaryController: Failed to load diary: ' . $e->getMessage());
            Session::flash('error', 'Failed to load diary. Please try again.');
            redirect('/dashboard');
        }

        view($user_role . '/diaries/index', [
            'title' => 'Weekly Diary',
            'diary' => $diary,
            'time_slots' => $time_slots,
            'class_id' => $class_id,
            'start_date' => (new \DateTime($start_date))->format('Y-m-d'),
            'user_role' => $user_role,
        ]);
    }

    public function dailyLessons(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
            redirect('/login');
        }

        $user_id = (int)$_SESSION['user']['id'];
        $date = $request->input('date') ?: date('Y-m-d');

        try {
            $lessons = Diary::query(
                "SELECT d.*, c.class_name, l.lesson_name, t.start, t.end
                 FROM diaries d
                 JOIN classes c ON d.class_id = c.id
                 JOIN lessons l ON d.lesson_id = l.id
                 JOIN time_slots t ON d.slot_number = t.slot_number
                 WHERE d.teacher_id = ? AND d.diary_date = ?
                 ORDER BY t.start",
                [$user_id, $date]
            )->getAll();

            view('teacher/diaries/daily', [
                'title' => 'Daily Lessons',
                'lessons' => $lessons,
                'date' => $date,
            ]);
        } catch (\Exception $e) {
            error_log('DiaryController: Failed to load daily lessons: ' . $e->getMessage());
            Session::flash('error', 'Failed to load daily lessons. Please try again.');
            redirect('/dashboard');
        }
    }

    public function create(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('DiaryController: Unauthorized access to create, redirecting to /login');
            redirect('/login');
        }

        $class_id = (int)$request->input('class_id');
        $diary_date = $request->input('diary_date');
        $slot_number = (int)$request->input('slot_number');

        try {
            $classes = ClassModel::all()->getAll();
            $lessons = Lesson::all()->getAll();
            $teachers = User::where('role', '=', 'teacher')->getAll();
        } catch (\Exception $e) {
            error_log('DiaryController: Failed to load create form data: ' . $e->getMessage());
            Session::flash('error', 'Failed to load form data. Please try again.');
            redirect('/diaries');
        }

        view('admin/diaries/create', [
            'title' => 'Add Diary Entry',
            'class_id' => $class_id,
            'diary_date' => $diary_date,
            'slot_number' => $slot_number,
            'classes' => $classes,
            'lessons' => $lessons,
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('DiaryController: Unauthorized access to store, redirecting to /login');
            redirect('/login');
        }

        $request->validate([
            'class_id' => 'required|numeric',
            'diary_date' => 'required|date',
            'slot_number' => 'required|numeric|min:1|max:8',
            'lesson_id' => 'required|numeric',
            'teacher_id' => 'required|numeric',
        ]);

        $data = [
            'class_id' => (int)$request->input('class_id'),
            'diary_date' => $request->input('diary_date'),
            'slot_number' => (int)$request->input('slot_number'),
            'lesson_id' => (int)$request->input('lesson_id'),
            'teacher_id' => (int)$request->input('teacher_id'),
        ];

        try {
            $existing = Diary::query(
                "SELECT id FROM diaries WHERE class_id = ? AND diary_date = ? AND slot_number = ?",
                [$data['class_id'], $data['diary_date'], $data['slot_number']]
            )->get();

            if ($existing) {
                Diary::update($existing['id'], $data);
            } else {
                Diary::create($data);
            }

            $teacher = User::find($data['teacher_id'])->get();
            $lesson = Lesson::find($data['lesson_id'])->get();
            $class = ClassModel::find($data['class_id'])->get();
            $body = "
                <h2>Lesson Scheduled</h2>
                <p><strong>Class:</strong> {$class['class_name']}</p>
                <p><strong>Lesson:</strong> {$lesson['lesson_name']}</p>
                <p><strong>Date:</strong> {$data['diary_date']}</p>
                <p><strong>Slot:</strong> " . Diary::getTimeSlots()[$data['slot_number']]['start'] . " - " . Diary::getTimeSlots()[$data['slot_number']]['end'] . "</p>
            ";
            Mail::send($teacher['email'], 'New Lesson Scheduled', $body);

            Session::flash('success', 'Diary entry added successfully.');
            redirect('/diaries?class_id=' . $data['class_id'] . '&week=' . $data['diary_date']);
        } catch (\Exception $e) {
            error_log('DiaryController: Failed to store diary entry: ' . $e->getMessage());
            Session::flash('error', 'Failed to add diary entry. Please try again.');
            redirect('/diaries/create?class_id=' . $data['class_id'] . '&diary_date=' . $data['diary_date'] . '&slot_number=' . $data['slot_number']);
        }
    }

    public function edit(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('DiaryController: Unauthorized access to edit, redirecting to /login');
            redirect('/login');
        }

        $id = (int)$request->input('id');

        try {
            $diary = Diary::find($id)->get();
            if (!$diary) {
                Session::flash('error', 'Diary entry not found.');
                redirect('/diaries');
            }
            $classes = ClassModel::all()->getAll();
            $lessons = Lesson::all()->getAll();
            $teachers = User::where('role', '=', 'teacher')->getAll();
        } catch (\Exception $e) {
            error_log('DiaryController: Failed to load edit form data: ' . $e->getMessage());
            Session::flash('error', 'Failed to load diary data. Please try again.');
            redirect('/diaries');
        }

        view('admin/diaries/edit', [
            'title' => 'Edit Diary Entry',
            'diary' => $diary,
            'classes' => $classes,
            'lessons' => $lessons,
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            error_log('DiaryController: Unauthorized access to update, redirecting to /login');
            redirect('/login');
        }

        $request->validate([
            'id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'diary_date' => 'required|date',
            'slot_number' => 'required|numeric|min:1|max:8',
            'lesson_id' => 'required|numeric',
            'teacher_id' => 'required|numeric',
        ]);

        $id = (int)$request->input('id');
        $data = [
            'class_id' => (int)$request->input('class_id'),
            'diary_date' => $request->input('diary_date'),
            'slot_number' => (int)$request->input('slot_number'),
            'lesson_id' => (int)$request->input('lesson_id'),
            'teacher_id' => (int)$request->input('teacher_id'),
        ];

        try {
            Diary::update($id, $data);

            $teacher = User::find($data['teacher_id'])->get();
            $lesson = Lesson::find($data['lesson_id'])->get();
            $class = ClassModel::find($data['class_id'])->get();
            $body = "
                <h2>Lesson Updated</h2>
                <p><strong>Class:</strong> {$class['class_name']}</p>
                <p><strong>Lesson:</strong> {$lesson['lesson_name']}</p>
                <p><strong>Date:</strong> {$data['diary_date']}</p>
                <p><strong>Slot:</strong> " . Diary::getTimeSlots()[$data['slot_number']]['start'] . " - " . Diary::getTimeSlots()[$data['slot_number']]['end'] . "</p>
            ";
            Mail::send($teacher['email'], 'Lesson Schedule Updated', $body);

            Session::flash('success', 'Diary entry updated successfully.');
            redirect('/student/diaries?class_id=' . $data['class_id'] . '&week=' . $data['diary_date']);
        } catch (\Exception $e) {
            error_log('DiaryController: Failed to update diary entry: ' . $e->getMessage());
            Session::flash('error', 'Failed to update diary entry. Please try again.');
            redirect('/student/diaries/' . $id . '/edit');
        }
    }
}
?>