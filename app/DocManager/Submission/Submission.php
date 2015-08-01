<?PHP

namespace DocManager\Submission;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Submission extends Eloquent
{
	protected $table "submissions";
	protected $primaryKey = "id_submission";
	public $timestamps = false;

	protected $fillable = [
		'id_user',
		'id_queue',
		'status',
		'is_approved',
		'created_at',
		'updated_last'
	]

	protected $guarded = [
		'id_submission'
	]
}