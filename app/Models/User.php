<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'DataMart.dbo.view_PersonBasic';
    protected $primaryKey = 'RCID';
    protected $connection = 'sqlsrv';
    protected $appends = ['display_name'];
    public $incrementing = false;

    public function getDisplayNameAttribute()
    {
        return $this->rc_full_name;
    }
}
