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
        if (is_string($roleName)) {
            foreach ($this->getRoles() as $role) {
                if (strcasecmp($role, $roleName) == 0) {
                    return true;
                }
            }
        } elseif (is_array($roleName)) {
            return count(array_intersect(array_values($this->getRoles()), $roleName));
        }

        return false;
    }

    public function getRoles()
    {
        $roles = array();
        foreach ($this->roles->toArray() as $role) {
            array_push($roles, $role['name']);
        }
        return $roles;
    }

    public function advisor()
    {
        return $this->hasOne('DocManager\User\User', 'id_user', 'id_advisor');
    }

    public function queues()
    {
        return $this->morphMany('DocManager\Queue\Queue', 'queueable');
    }

    public function coursequeues()
    {
        return $this->hasManyThrough('DocManager\Queue\Queue', 'DocManager\Course\Course', 'id_coordinator', 'id_queue');
    }

    public function courses()
    {
        return $this->hasMany('DocManager\Course\Course', 'id_coordinator', 'id_user');
    }

    public function submissions()
    {
        return $this->hasMany('DocManager\Submission\Submission', 'id_user', 'id_user');
    }

    public function comments()
    {
        return $this->hasMany('DocManager\Comment\Comment', 'id_user', 'id_user');
    }

    public function name()
    {
        return "$this->last_name, $this->first_name";
    }
}
