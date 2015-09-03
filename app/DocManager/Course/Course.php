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

    public function coordinator()
    {
        return $this->hasOne('DocManager\User\User', 'id_user', 'id_coordinator');
    }

    public function addedBy()
    {
        return $this->hasOne('DocManager\User\User', 'id_user', 'id_added_by');
    }

    public function queues()
    {
        return $this->morphMany('DocManager\Queue\Queue', 'queueable');
    }

    // Override existing delete method to remove related models
    public function delete()
    {
        // delete all associated queues
        $this->queues->each(function ($queue) {
            $queue->delete();
        });
        
        // delete the course
        return parent::delete();
    }
}
