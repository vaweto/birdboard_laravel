<?php


namespace Tests\Setup;


use App\Project;
use App\Task;
use App\User;

class ProjectFactory
{
    protected $taskCount = 0;

    protected $user;

    public function create()
    {
        $project = factory(Project::class)->create([
           'owner_id' =>  $this->user ?? factory(User::class)->create()
        ]);

        factory(Task::class, $this->taskCount)->create([
           'project_id' => $project->id
        ]);

        return $project;
    }

    public function withTasks($count)
    {
        $this->taskCount = $count;

        return $this;
    }

    public function ownedBy($user)
    {
        $this->user = $user;

        return $this;
    }
}