<?php

namespace DocManager\Comment;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Comment extends Eloquent
{
    protected $table = "comments";
    protected $primaryKey = "id_comment";
    public $timestamps = false;
    // Update parents updated_at timestamp when this is modified or created
    protected $touches = array('commentable');

    protected $fillable = [
        'id_user',
        'comment',
        'created_at'
    ];

    protected $guarded = [
        'id_comment',
        'commentable_type',
        'commentable_id'
    ];

    /*
        Define our polymorphic relationship for this model
        Currently, Submission is the only model that can be commented on.
        This polymorphic design allows comments to belong to multiple models in the future. 
        The commentable_type field stores the class name of the owning model
        The commentable_id field stores the ID of the owning model
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('DocManager\User\User', 'id_user', 'id_user');
    }
}
