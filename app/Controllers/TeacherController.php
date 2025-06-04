<?php
namespace App\Controllers;

use App\Models\ClassModel;
use Core\Request;
use Core\Session;

class TeacherController extends Controller
{
    public function index(Request $request): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
            redirect('/login');
        }

        $user_id = (int)$_SESSION['user']['id'];

        try {
            $assigned_classes = ClassModel::query(
                "SELECT DISTINCT c.* FROM classes c 
                 JOIN class_lesson_teachers clt ON c.id = clt.class_id 
                 WHERE clt.teacher_id = ?",
                [$user_id]
            )->getAll();
        } catch (\Exception $e) {
            error_log('TeacherController: Failed to load assigned classes: ' . $e->getMessage());
            Session::flash('error', 'Failed to load classes. Please try again.');
            redirect('/dashboard');
        }

        view('teacher/classes', [
            'title' => 'My Classes',
            'classes' => $assigned_classes,
        ]);
    }
}
?>