<?php

namespace DocManager\Queue;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Queue extends Eloquent
{

    protected $table = "queues";
    protected $primaryKey = "id_queue";
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'is_enabled'
    ];

    protected $guarded = [
        'id_queue',
        'queueable_type',
        'queueable_id'
    ];

    /*
        Define the one-to-many relationship
     */
    public function elements()
    {
        return $this->hasMany('DocManager\Queue\QueueElement', 'id_queue', 'id_queue');
    }

    /*
        Define our polymorphic relationship for this model
        Queues can belong to either Courses or Users
        The queueable_type field stores the class name of the owning model
        The queueable_id field stores the ID of the owning model
     */
    public function queueable()
    {
        return $this->morphTo();
    }
}
