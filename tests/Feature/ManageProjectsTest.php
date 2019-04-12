<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;


    /** @test */
    public function a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->text,
            'notes' => 'general notes'
        ];

        $this->get('/projects/create')->assertStatus(200);

        $response = $this->post('/projects',$attributes);

        $project = Project::where($attributes)->first();

        $response->assertRedirect($project->path());


        $this->assertDatabaseHas('projects',$attributes);

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    /** @test */
    public function a_user_can_update_their_project()
    {
        $this->signIn();
        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

        $this->patch($project->path(),[
           'notes' => 'changed'
        ])->assertRedirect($project->path());

        $this->assertDatabaseHas('projects',['notes' => 'changed']);
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $this->signIn();
        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);

    }

    /** @test */
    public function an_authitincated_user_cannot_see_other_user_projects()
    {

        $this->signIn();

        $other_user = factory('App\User')->create();

        $project = factory('App\Project')->create(['owner_id' => $other_user->id]);

        $this->get($project->path())->assertStatus(403);
    }

    /** @test */
    public function an_authitincated_user_cannot_update_other_user_projects()
    {

        $this->signIn();

        $project = factory('App\Project')->create();

        $this->patch($project->path(),[])->assertStatus(403);
    }

    /** @test */
    public function a_project_requires_a_title()
    {
        $this->signIn();

        $attributes = factory('App\Project')->raw(['title' => '']);
        $this->post('/projects',$attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_requires_a_description()
    {
        $this->signIn();

        $attributes = factory('App\Project')->raw(['description' => '']);
        $this->post('/projects',$attributes)->assertSessionHasErrors('description');
    }

    /** @test */
    public function only_authinticated_users_can_create_projects()
    {
        $attributes = factory('App\Project')->raw();
        $this->post('/projects',$attributes)->assertRedirect('login');
    }

    /** @test */
    public function guest_may_not_view_projects()
    {
        $this->get('/projects')->assertRedirect('login');
    }

    /** @test */
    public function guest_may_not_view_project()
    {
        $project = factory('App\Project')->create();

        $this->get($project->path())->assertRedirect('login');
    }

    /** @test */
    public function guest_may_not_view_create_project_page()
    {

        $this->get('/projects/create')->assertRedirect('login');
    }
}
