<?php

namespace App\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;

class GradeController
{
    // Просмотр оценок для текущего студента
    public function viewGrades($studentId)
    {
        // Получаем оценки студента
        $grades = Grade::join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->where('grades.student_id', '=', $studentId)
            ->get(['subjects.subject_name', 'grades.grade', 'grades.date']);
        
        if (empty($grades)) {
            echo "You have no grades.";
        } else {
            foreach ($grades as $grade) {
                echo "Subject: " . htmlspecialchars($grade->subject_name) . "<br>";
                echo "Grade: " . htmlspecialchars($grade->grade) . "<br>";
                echo "Date: " . htmlspecialchars($grade->date) . "<br><br>";
            }
        }
    }

    // Фильтрация студентов по имени или фамилии
    public function filterStudents($filter)
    {
        $students = Student::where('first_name', 'LIKE', "%$filter%")
            ->orWhere('last_name', 'LIKE', "%$filter%")
            ->orderBy('first_name', 'asc')
            ->get();

        foreach ($students as $student) {
            echo "Name: " . htmlspecialchars($student->first_name) . " Last Name: " . htmlspecialchars($student->last_name) . "<br>";
        }
    }
}
