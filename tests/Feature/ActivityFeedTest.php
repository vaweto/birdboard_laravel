<?php

namespace Tests\Feature;

use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function creating_a_project_records_activity()
    {
        $project = ProjectFactory::create();

        $this->assertCount(1,$project->activity);
        $this->assertEquals('created',$project->activity[0]->description);
    }

    /** @test */
    function updating_a_project_records_activity()
    {
        $project = ProjectFactory::create();
        $project->update(['title' => 'Changed']);

        $this->assertCount(2,$project->activity);
        $this->assertEquals('updated',$project->activity->last()->description);
    }

    /** @test */
    public function create_a_new_task_records_project_activity()
    {
        $project = ProjectFactory::create();

        $project->addTask('some taski');

        $this->assertCount(2, $project->activity);
    }

    /** @test */
    public function completing_a_new_task_records_project_activity()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $this->actingAs($project->owner)->patch($project->tasks[0]->path(),[
            'body' => 'foobar',
            'completed' => 1
        ]);

        $this->assertCount(3, $project->activity);
        $this->assertEquals('completed',$project->activity->last()->description);
    }
}
