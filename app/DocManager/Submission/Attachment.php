<?php

namespace DocManager\Submission;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Attachment extends Eloquent
{
    protected $table = "submission_attachments";
    protected $primaryKey = "id_attachment";
    // Update parents updated_at timestamp when this is modified or created
    protected $touches = array('submission');

    protected $fillable = [
        'id_submission',
        'id_element',
        'name',
        'size',
        'content_type',
        'content'
    ];

    protected $guarded = [
        'id_attachment',
        'created_at',
        'updated_at'
    ];

    public function submission()
    {
        return $this->belongsTo('DocManager\Submission\Submission', 'id_submission', 'id_submission');
    }

    // Queue element that this attachment is related to
    public function element()
    {
        return $this->belongsTo('DocManager\Queue\QueueElement', 'id_element', 'id_element');
    }
}
