<?php

namespace App\Controllers;

use App\Models\Mail;
use App\Models\User;
use App\Models\ClassModel;
use Core\Request;
use Core\Session;

class MailController extends Controller
{
    protected static string $model = 'Mail';

    public function index(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $user_id = (int)$_SESSION['user']['id'];
        $user_role = $_SESSION['user']['role'];

        try {
            // Fetch inbox (received mails)
            $inbox = Mail::query(
                "SELECT m.*, u.first_name, u.last_name 
                 FROM mail m 
                 JOIN users u ON m.sender_id = u.id 
                 WHERE m.receiver_id = ? 
                 ORDER BY m.sent_at DESC",
                [$user_id]
            )->getAll();

            // Fetch sent mails
            $sent = Mail::query(
                "SELECT m.*, u.first_name, u.last_name 
                 FROM mail m 
                 JOIN users u ON m.receiver_id = u.id 
                 WHERE m.sender_id = ? 
                 ORDER BY m.sent_at DESC",
                [$user_id]
            )->getAll();

            // Fetch allowed recipients (same class or school staff)
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
            error_log('MailController: Failed to load mail data: ' . $e->getMessage());
            Session::flash('error', 'Failed to load mailbox. Please try again.');
            redirect('/mail');
        }

        view('mail/index', [
            'title' => 'Mailbox',
            'inbox' => $inbox,
            'sent' => $sent,
            'recipients' => $recipients,
        ]);
    }

    public function create(): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $user_id = (int)$_SESSION['user']['id'];
        $user_role = $_SESSION['user']['role'];

        try {
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
            error_log('MailController: Failed to load create form: ' . $e->getMessage());
            Session::flash('error', 'Failed to load form data. Please try again.');
            redirect('/mail');
        }

        view('mail/create', [
            'title' => 'Compose Mail',
            'recipients' => $recipients,
        ]);
    }

    public function store(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
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

        try {
            Mail::create($data);
            Session::flash('success', 'Mail sent successfully.');
            redirect('/mail');
        } catch (\Exception $e) {
            error_log('MailController: Failed to send mail: ' . $e->getMessage());
            Session::flash('error', 'Failed to send mail. Please try again.');
            redirect('/mail/create');
        }
    }

    public function show(Request $request): void
    {
        if (!isset($_SESSION['user'])) {
            redirect('/login');
        }

        $id = (int)$request->input('id');
        $user_id = (int)$_SESSION['user']['id'];

        try {
            $mail = Mail::query(
                "SELECT m.*, u.first_name, u.last_name 
                 FROM mail m 
                 JOIN users u ON m.sender_id = u.id 
                 WHERE m.id = ? AND (m.sender_id = ? OR m.receiver_id = ?)",
                [$id, $user_id, $user_id]
            )->get();
            if (!$mail) {
                Session::flash('error', 'Mail not found.');
                redirect('/mail');
            }
            if ($mail['receiver_id'] == $user_id && !$mail['is_read']) {
                Mail::update($mail['id'], ['is_read' => 1]);
            }
        } catch (\Exception $e) {
            error_log('MailController: Failed to load mail: ' . $e->getMessage());
            Session::flash('error', 'Failed to load mail. Please try again.');
            redirect('/mail');
        }

        view('mail/show', [
            'title' => 'View Mail',
            'mail' => $mail,
        ]);
    }
}