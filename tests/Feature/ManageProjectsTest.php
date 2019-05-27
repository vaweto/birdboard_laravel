<?php

namespace Tests\Feature;

use App\Project;
use App\User;
use Facades\Tests\Setup\ProjectFactory;
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

        $attributes = factory(Project::class)->raw(['owner_id' => auth()->id()]);

        $this->get('/projects/create')->assertStatus(200);

        $response = $this->post('/projects',$attributes);

        $project = Project::where($attributes)->first();

        $response->assertRedirect($project->path());

        $this->assertDatabaseHas('projects',$attributes);

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);;
    }

    /** @test */
    public function tasks_can_be_include_as_part_of_new_project()
    {
        $this->signIn();

        $attributes = factory(Project::class)->raw();

        $attributes['tasks'] = [
          ['body' => 'task 1'],
          ['body' => 'task 2'],
        ];

        $this->post('/projects',$attributes);

        $this->assertCount(2,Project::first()->tasks);

    }

    /** @test */
    public function empty_tasks_does_not_be_included_as_part_of_new_project()
    {
        $this->signIn();

        $attributes = factory(Project::class)->raw();

        $attributes['tasks'] = [
            ['body' => ''],
        ];

        $this->post('/projects',$attributes);

        $this->assertCount(0,Project::first()->tasks);

    }

    /** @test */
    public function a_user_can_update_their_project()
    {

        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
            ->patch($project->path(), $attributes = [
               'notes' => 'changed',
                'title' => 'Changed',
                'description' => 'Changed',
            ])
            ->assertRedirect($project->path());

        $this->get($project->path().'/edit')->assertOk();

        $this->assertDatabaseHas('projects',$attributes);
    }

    /** @test */
    public function guest_cannot_delete_their_project()
    {


        $project = ProjectFactory::create();

        $this->delete($project->path())
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete($project->path())->assertStatus(403);

    }

    /** @test */
    public function invited_members_cannot_delete_their_project()
    {
        $project = ProjectFactory::create();
        $user = factory(User::class)->create();

        $project->invite($user);

        $this->actingAs($user)->delete($project->path())
            ->assertStatus(403);

    }

    /** @test */
    public function a_user_can_delete_their_project()
    {
        $this->withoutExceptionHandling();

        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
            ->delete($project->path())
            ->assertRedirect('/projects');

        $this->assertDatabaseMissing('projects',['id' => $project->id]);
    }

    /** @test */
    public function a_user_can_update_only_the_notes()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
            ->patch($project->path(), $atributes = [
                'notes' => 'changed'
            ])
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('projects',$atributes);
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
             ->get($project->path())
             ->assertSee($project->title)
             ->assertSee($project->description);

    }

    /** @test */
    public function a_user_can_see_all_projects_they_have_invited_to_on_their_dashboard()
    {
        $project = ProjectFactory::create();

        $project->invite($newUser = factory(User::class)->create());

        $this->actingAs($newUser)
            ->get('/projects')
            ->assertSee($project->title)
            ->assertSee($project->description);

    }

    /** @test */
    public function an_authitincated_user_cannot_see_other_user_projects()
    {
        $other_user = factory('App\User')->create();

        $project = ProjectFactory::create();

        $this->actingAs($other_user)
            ->get($project->path())
            ->assertStatus(403);
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
    public function guest_may_not_editproject()
    {
        $project = factory('App\Project')->create();

        $this->get($project->path().'/edit')->assertRedirect('login');
    }

    /** @test */
    public function guest_may_not_view_create_project_page()
    {

        $this->get('/projects/create')->assertRedirect('login');
    }
}
