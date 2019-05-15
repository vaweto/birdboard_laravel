<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Activity;

class Project extends Model
{
    //

    use RecordsActivity;

    protected $guarded = [];

    protected $recordableActivity = ['created', 'deleted', 'updated'];

    public function path()
    {
        return "/projects/{$this->id}";
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function addTask($body)
    {
       $task = $this->tasks()->create(['body' => $body]);

       return $task;
    }

    public function activity()
    {
        return $this->hasMany(Activity::class)->latest();
    }


}
