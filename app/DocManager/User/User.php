<?php

namespace DocManager\User;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    protected $table = 'Users';
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'major',
        'password',
        'id_advisor'
    ];
}
