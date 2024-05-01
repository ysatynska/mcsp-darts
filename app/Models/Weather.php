<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weather extends Model
{
    protected $table = 'weather';

    protected $connection = 'DailyEmail';
}
