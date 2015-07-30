<?php

namespace DocManager\Course;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Course extends Eloquent
{
    protected $table = 'courses';
    protected $primaryKey = "id_course";
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'id_added_by',
        'id_coordinator'
    ];

    protected $guarded = [
        'id_course'
    ];
}
