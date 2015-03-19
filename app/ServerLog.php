<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ServerLog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'server_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['server_id', 'deploy_step_id', 'status', 'output'];

    public function server()
    {
        return $this->belongsTo('App\Server');
    }

    public function getDates()
    {
        return ['created_at', 'started_at', 'finished_at', 'updated_at'];
    }

    public function runtime()
    {
        if (!$this->finished_at) {
            return false;
        }

        return $this->started_at->diffInSeconds($this->finished_at);
    }
}
