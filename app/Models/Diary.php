<?php

namespace App\Models;

use Core\Model;

class Diary extends Model
{
    protected static string $table = 'diaries';

    public static function initializeWeek(int $class_id, string $start_date): void
    {
        // Ensure diary entries exist for the week (optional, as admin pre-assigns)
        // For now, assume entries are created manually via create/store
    }

    public static function getWeekDiary(int $class_id, string $start_date): array
    {
        $start = new \DateTime($start_date);
        $start->modify('Monday this week');
        $end = clone $start;
        $end->modify('+4 days'); // Mon-Fri

        $query = "SELECT d.*, l.lesson_name, u.first_name, u.last_name
                  FROM diaries d
                  LEFT JOIN lessons l ON d.lesson_id = l.id
                  LEFT JOIN users u ON d.teacher_id = u.id
                  WHERE d.class_id = ? AND d.diary_date BETWEEN ? AND ?
                  ORDER BY d.diary_date, d.slot_number";
        
        return static::query($query, [
            $class_id,
            $start->format('Y-m-d'),
            $end->format('Y-m-d')
        ])->getAll();
    }

    public static function getTimeSlots(): array
    {
        return [
            1 => ['start' => '08:00', 'end' => '08:45'],
            2 => ['start' => '09:00', 'end' => '09:45'],
            3 => ['start' => '10:00', 'end' => '10:45'],
            4 => ['start' => '11:00', 'end' => '11:45'],
            5 => ['start' => '12:00', 'end' => '12:45'],
            6 => ['start' => '13:00', 'end' => '13:45'],
            7 => ['start' => '14:00', 'end' => '14:45'],
            8 => ['start' => '15:00', 'end' => '15:45'],
        ];
    }
}