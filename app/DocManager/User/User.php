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

    public function roles()
    {
        return $this->belongsToMany('DocManager\Role\Role', 'user_roles', 'id_user', 'id_role');
    }

    public function hasRole($roleName)
    {
        foreach ($this->roles as $role) {
            if (strcasecmp($role['name'], $roleName)) {
                return true;
            }
        }

        return false;
    }
}
