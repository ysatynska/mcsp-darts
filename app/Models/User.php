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
        $first_name = $this->FirstName;

        if (! empty($this->Nickname)) {
            $first_name = $this->Nickname;
        }

        return sprintf('%s %s', trim($first_name), trim($this->LastName));
    }
}
