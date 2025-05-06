<?php

namespace App\Models;

use Core\Model;

class Student extends Model
{
    // Таблица в базе данных
    protected static $table = 'students';

    // Заполняемые поля
    protected $fillable = ['first_name', 'last_name'];
}
