<?PHP

namespace DocManager\Queue;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Queue extends Eloquent
{
	protected $table "queues";
	protected $primaryKey = "id_queue";
	public $timestamps = false;

	protected $fillable = [
		'queue_type',
		'name',
		'description',
		'order_by',
		'id_course',
		'id_user',
		'is_enabled'
	]

	protected $guarded = [
		'id_queue'
	]
}