<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function guest_cannot_add_tasks_to_projects()
    {
        $project = factory('App\Project')->create();

        $this->post($project->path(). '/tasks')->assertRedirect('login');
    }

    /** @test */
    public function only_the_owner_of_a_project_may_add_tasks()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks', ['body' => 'Test task'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks',['body' => 'Test task']);
    }

    /** @test */
    public function only_the_owner_of_a_project_may_update_tasks()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $task = $project->addTask('test task');

        $this->patch($task->path() , ['body' => 'Test task 1'])
            ->assertStatus(403);

        $this->assertDatabasehas('tasks',['body' => 'test task']);
    }

    /** @test */
    public function a_project_can_have_tasks()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $project = factory(Project::class)->create(['owner_id' => auth()->id() ]);

        $this->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
            ->assertSee('Test task');
    }

    /** @test */
    public function a_task_can_be_updated()
    {
        $this->signIn();
        $this->withoutExceptionHandling();

        $project = auth()->user()->projects()->create(
            factory(Project::class)->raw()
        );

        $task = $project->addTask('test task');

        $this->patch($task->path(), ['body' => 'Test1 task', 'completed' => true]);

        $this->assertDatabaseHas('tasks',['body' => 'Test1 task', 'completed' => true]);

    }

    /** @test */
    public function a_task_requires_a_body()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['owner_id' => auth()->id() ]);

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->post($project->path() . '/tasks')->assertSessionHasErrors('body');

    }

}
