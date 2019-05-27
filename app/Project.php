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

    protected static $recordableEvents = ['created', 'updated'];

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

    /**
    * Add a task to the project.
    *
    * @param array $tasks
    * @return \Illuminate\Database\Eloquent\Collection
    */
    public function addTasks($tasks)
    {
        return $this->tasks()->createMany($tasks);
    }

    public function activity()
    {
        return $this->hasMany(Activity::class)->latest();
    }

    public function invite(User $user)
    {
        return $this->members()->attach($user);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')->withTimestamps();
    }


}
