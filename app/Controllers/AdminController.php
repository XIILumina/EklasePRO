<?php
namespace App\Controllers;

use App\Models\Mail;
use App\Models\User;
use App\Models\ClassModel;
use Core\Request;
use Core\Session;

class AdminController extends Controller
{
    protected static string $model = 'Mail';

    public function index(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            error_log('MailController::index: No user session, redirecting to /login');
            redirect('/login');
        }

        $user_id = (int)$_SESSION['user']['id'];
        $user_role = $_SESSION['user']['role'];

        try {
            $inbox = Mail::query(
                "SELECT m.*, u.first_name, u.last_name 
                 FROM mail m 
                 JOIN users u ON m.sender_id = u.id 
                 WHERE m.receiver_id = ? 
                 ORDER BY m.sent_at DESC",
                [$user_id]
            )->getAll();

            $sent = Mail::query(
                "SELECT m.*, u.first_name, u.last_name 
                 FROM mail m 
                 JOIN users u ON m.receiver_id = u.id 
                 WHERE m.sender_id = ? 
                 ORDER BY m.sent_at DESC",
                [$user_id]
            )->getAll();

            $recipients = [];
            if ($user_role === 'student') {
                $class = ClassModel::query(
                    "SELECT class_id FROM class_students WHERE user_id = ?",
                    [$user_id]
                )->get();
                if ($class) {
                    $recipients = User::query(
                        "SELECT u.* FROM users u 
                         LEFT JOIN class_students cs ON u.id = cs.user_id 
                         WHERE (cs.class_id = ? OR u.role IN ('teacher', 'admin')) 
                         AND u.id != ?",
                        [$class['class_id'], $user_id]
                    )->getAll();
                }
            } else {
                $recipients = User::query(
                    "SELECT * FROM users WHERE id != ?",
                    [$user_id]
                )->getAll();
            }
        } catch (\Exception $e) {
            error_log('MailController::index: Failed to load mail data: ' . $e->getMessage());
            Session::flash('error', 'Failed to load mailbox. Please try again.');
            redirect('/mail');
        }

        view('public/mail/index', [
            'title' => 'Mailbox',
            'inbox' => $inbox,
            'sent' => $sent,
            'recipients' => $recipients,
        ]);
    }

    public function create(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            error_log('MailController::create: No user session, redirecting to /login');
            redirect('/login');
        }

        $user_id = (int)$_SESSION['user']['id'];
        $user_role = $_SESSION['user']['role'];
        $reply_to = (int)$request->input('reply_to', 0);
        $reply_mail = null;
        $reply_recipient = null;

        try {
            if ($reply_to) {
                $reply_mail = Mail::query(
                    "SELECT m.*, u.first_name, u.last_name 
                     FROM mail m 
                     JOIN users u ON m.sender_id = u.id 
                     WHERE m.id = ? AND m.receiver_id = ?",
                    [$reply_to, $user_id]
                )->get();
                if ($reply_mail) {
                    $reply_recipient = User::find($reply_mail['sender_id'])->get();
                }
            }

            $recipients = [];
            if ($user_role === 'student') {
                $class = ClassModel::query(
                    "SELECT class_id FROM class_students WHERE user_id = ?",
                    [$user_id]
                )->get();
                if ($class) {
                    $recipients = User::query(
                        "SELECT u.* FROM users u 
                         LEFT JOIN class_students cs ON u.id = cs.user_id 
                         WHERE (cs.class_id = ? OR u.role IN ('teacher', 'admin')) 
                         AND u.id != ?",
                        [$class['class_id'], $user_id]
                    )->getAll();
                }
            } else {
                $recipients = User::query(
                    "SELECT * FROM users WHERE id != ?",
                    [$user_id]
                )->getAll();
            }
        } catch (\Exception $e) {
            error_log('MailController::create: Failed to load create form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load form data. Please try again.');
            redirect('/mail');
        }

        view('public/mail/create', [
            'title' => 'Compose Mail',
            'recipients' => $recipients,
            'reply_mail' => $reply_mail,
            'reply_recipient' => $reply_recipient,
        ]);
    }

    public function store(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            error_log('MailController::store: No user session, redirecting to /login');
            redirect('/login');
        }

        $request->validate([
            'receiver_id' => 'required|numeric',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $data = [
            'sender_id' => (int)$_SESSION['user']['id'],
            'receiver_id' => (int)$request->input('receiver_id'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
        ];

        if ($data['sender_id'] === $data['receiver_id']) {
            Session::flash('error', 'You cannot send mail to yourself.');
            redirect('/mail/create');
        }

        try {
            Mail::create($data);
            Session::flash('success', 'Mail sent successfully.');
            redirect('/mail');
        } catch (\Exception $e) {
            error_log('MailController::store: Failed to send mail: ' . $e->getMessage());
            Session::flash('error', 'Failed to send mail. Please try again.');
            redirect('/mail/create');
        }
    }

    public function show(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            error_log('MailController::show: No user session, redirecting to /login');
            redirect('/login');
        }

        $id = (int)$request->input('id');
        $user_id = (int)$_SESSION['user']['id'];

        error_log("MailController::show: Mail ID requested: " . $id . ", User ID: " . $user_id);

        try {
            $mail = Mail::query(
                "SELECT m.*, u.first_name, u.last_name 
                 FROM mail m 
                 JOIN users u ON m.sender_id = u.id 
                 WHERE m.id = ? AND (m.sender_id = ? OR m.receiver_id = ?)",
                [$id, $user_id, $user_id]
            )->get();

            error_log("MailController::show: Mail retrieved: " . var_export($mail, true));

            if (!$mail) {
                error_log("MailController::show: Mail ID $id not found or not accessible for user $user_id");
                Session::flash('error', 'Mail not found or you do not have access.');
                redirect('/mail');
            }

            $required_fields = ['id', 'subject', 'body', 'sent_at', 'first_name', 'last_name', 'receiver_id', 'is_read'];
            foreach ($required_fields as $field) {
                if (!isset($mail[$field])) {
                    error_log("MailController::show: Missing required field '$field' in mail data");
                    Session::flash('error', 'Invalid mail data.');
                    redirect('/mail');
                }
            }

            if ($mail['receiver_id'] == $user_id && !$mail['is_read']) {
                Mail::update($mail['id'], ['is_read' => 1]);
                error_log("MailController::show: Marked mail ID $id as read for user $user_id");
            }

            error_log("MailController::show: Rendering view for mail ID: " . $id);

            view('public/mail/show', [
                'title' => 'View Mail',
                'mail' => $mail,
            ]);
        } catch (\Exception $e) {
            error_log('MailController::show: Failed to load mail: ' . $e->getMessage());
            Session::flash('error', 'Failed to load mail. Please try again.');
            redirect('/mail');
        }
    }
}