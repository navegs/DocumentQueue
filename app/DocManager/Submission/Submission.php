<?php

namespace DocManager\Submission;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Submission extends Eloquent
{
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_AWAITING_REVIEW = 'AWAITING REVIEW';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    protected $table = "submissions";
    protected $primaryKey = "id_submission";

    protected $fillable = [
        'id_user',
        'id_queue',
        'status'
    ];

    protected $guarded = [
        'id_submission',
        'created_at',
        'updated_at'
    ];

     /*
        Define the inverse lookup one-to-many relationship
     */
    public function queue()
    {
        return $this->belongsTo('DocManager\Queue\Queue', 'id_queue', 'id_queue');
    }

     /*
        Define the inverse lookup one-to-many relationship
     */
    public function user()
    {
        return $this->belongsTo('DocManager\User\User', 'id_user', 'id_user');
    }

    /*
        Define the one-to-many relationship
     */
    public function comments()
    {
        return $this->morphMany('DocManager\Comment\Comment', 'commentable');
    }

    /*
        Define the one-to-many relationship
     */
    public function attachments()
    {
        return $this->hasMany('DocManager\Submission\Attachment', 'id_submission', 'id_submission');
    }
}
