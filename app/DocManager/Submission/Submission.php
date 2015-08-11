<?PHP

namespace DocManager\Submission;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Submission extends Eloquent
{
    protected $table = "submissions";
    protected $primaryKey = "id_submission";

    protected $fillable = [
        'id_user',
        'id_queue',
        'status',
        'is_approved'
    ];

    protected $guarded = [
        'id_submission',
        'created_at',
        'updated_last'
    ];

    public function queue()
    {
        return $this->belongsTo('DocManager\Queue\Queue', 'id_queue', 'id_queue');
    }

    public function user()
    {
        return $this->belongsTo('DocManager\User\User', 'id_user', 'id_user');
    }
}
