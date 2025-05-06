<?php

namespace App\Models;

use Core\Model;

class Grade extends Model
{
    // Таблица в базе данных
    protected static $table = 'grades';

    // Заполняемые поля
    protected $fillable = ['student_id', 'subject_id', 'grade', 'date'];
}
