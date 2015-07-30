<?php

namespace DocManager\Course;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Course extends Eloquent
{
    protected $table = 'courses';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'id_added_by',
        'id_coordinator'
    ];
}
