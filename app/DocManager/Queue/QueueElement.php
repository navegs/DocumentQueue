<?php

namespace DocManager\Queue;

use Illuminate\Database\Eloquent\Model as Eloquent;

class QueueElement extends Eloquent
{

    protected $table = "queue_elements";
    protected $primaryKey = "id_element";
    public $timestamps = false;

    protected $fillable = [
        'id_queue',
        'name',
        'description'
    ];

    protected $guarded = [
        'id_element'
    ];

    public function queue()
    {
        return $this->belongsTo('DocManager\Queue\Queue', 'id_queue');
    }
}
