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
    public function a_task_requires_a_body()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['owner_id' => auth()->id() ]);

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->post($project->path() . '/tasks')->assertSessionHasErrors('body');

    }

}
