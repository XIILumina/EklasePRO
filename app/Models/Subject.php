<?php

namespace App\Models;

use Core\Model;

class Subject extends Model
{
    // Таблица в базе данных
    protected static  $table = 'subjects';

    // Заполняемые поля
    protected $fillable = ['subject_name'];
}
