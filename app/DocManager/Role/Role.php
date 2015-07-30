<?php

namespace DocManager\Role;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Role extends Eloquent
{
    protected $table = 'roles';
    protected $primaryKey = "id_role";
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    protected $guarded = [
        'id_role'
    ];

    public function users()
    {
        return $this->belongsToMany('DocManager\User\User', 'user_roles', 'id_role', 'id_user');
    }
}
