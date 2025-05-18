<?php

namespace App\Controllers;

use App\Models\Detention;
use App\Models\User;
use App\Models\ClassModel;
use Core\Request;
use Core\Session;
use Core\Mail;

class DetentionController extends Controller
{
    protected static string $model = 'Detention';

    public function index(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $user_role = $_SESSION['user']['role'];
        $user_id = (int)$_SESSION['user']['id'];

        try {
            if ($user_role === 'student') {
                $detentions = Detention::query(
                    "SELECT d.*, u.first_name, u.last_name 
                     FROM detentions d 
                     JOIN users u ON d.teacher_id = u.id 
                     WHERE d.student_id = ? 
                     ORDER BY d.detention_date DESC",
                    [$user_id]
                )->getAll();
            } else {
                $detentions = Detention::query(
                    "SELECT d.*, u.first_name, u.last_name 
                     FROM detentions d 
                     JOIN users u ON d.student_id = u.id 
                     WHERE d.teacher_id = ? 
                     ORDER BY d.detention_date DESC",
                    [$user_id]
                )->getAll();
            }
        } catch (\Exception $e) {
            error_log('DetentionController: Failed to load detentions: ' . $e->getMessage());
            Session::flash('error', 'Failed to load detentions. Please try again.');
            redirect('/detentions');
        }

        view('detentions/index', [
            'title' => 'Detentions',
            'detentions' => $detentions,
        ]);
    }

    public function create(Request $request): void
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['teacher', 'admin'])) {
            redirect('/login');
        }

        $class_id = (int)$request->input('class_id');

        try {
            $students = $class_id ? User::query(
                "SELECT u.* FROM users u JOIN class_students cs ON u.id = cs.user_id WHERE cs.class_id = ? AND u.role = 'student'",
                [$class_id]
            )->getAll() : [];
            $classes = ClassModel::all()->getAll();
        } catch (\Exception $e) {
            error_log('DetentionController: Failed to load create form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load form data. Please try again.');
            redirect('/detentions');
        }

        view('detentions/create', [
            'title' => 'Assign Detention',
            'students' => $students,
            'classes' => $classes,
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
            'reason' => 'required|string',
            'detention_date' => 'required|date',
        ]);

        $data = [
            'student_id' => (int)$request->input('student_id'),
            'teacher_id' => (int)$_SESSION['user']['id'],
            'reason' => $request->input('reason'),
            'detention_date' => $request->input('detention_date'),
        ];

        try {
            Detention::create($data);

            $student = User::find($data['student_id'])->get();
            $body = "
                <h2>Detention Assigned</h2>
                <p><strong>Reason:</strong> {$data['reason']}</p>
                <p><strong>Date:</strong> {$data['detention_date']}</p>
            ";
            Mail::send($student['email'], 'Detention Assigned', $body);

            Session::flash('success', 'Detention assigned successfully.');
            redirect('/detentions');
        } catch (\Exception $e) {
            error_log('DetentionController: Failed to assign detention: ' . $e->getMessage());
            Session::flash('error', 'Failed to assign detention. Please try again.');
            redirect('/detentions/create');
        }
    }
}