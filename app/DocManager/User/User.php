<?php

namespace DocManager\User;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    protected $table = 'users';
    protected $primaryKey = "id_user";
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'major',
        'password',
        'id_advisor'
    ];

    protected $guarded = [
        'id_user'
    ];
}
