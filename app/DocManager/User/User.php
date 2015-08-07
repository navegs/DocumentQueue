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
        foreach ($this->getRoles() as $role) {
            if (strcasecmp($role['name'], $roleName) == 0) {
                return true;
            }
        }

        return false;
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function advisor()
    {
        return $this->hasOne('DocManager\User\User', 'id_user', 'id_advisor');
    }
}
