<?php

namespace Tests\Feature;


use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function non_owners_may_not_invite_users()
    {
        $project = ProjectFactory::create();

        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post($project->path() . '/invitations')
            ->assertStatus(403);

        $project->invite($user);

        $this->actingAs($user)
            ->post($project->path() . '/invitations')
            ->assertStatus(403);
    }

    /** @test */
    function a_projects_owner_can_invite_a_users()
    {
        $this->withoutExceptionHandling();

        $project = ProjectFactory::create();

        $userToInvite = factory(User::class)->create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/invitations', [
            'email' => $userToInvite->email
            ])
            ->assertRedirect($project->path());

        $this->assertTrue($project->members->contains($userToInvite));
    }

    /** @test */
    function the_invited_email_address_mustbe_valid_biboard_account()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/invitations', [
            'email' => 'not@user.com'])
            ->assertSessionHasErrors(
                ['email' => 'must have a app account'],
                null,
                'invitations'
            );

    }

    /** @test */
    function invited_users_may_update_projects_details()
    {
        $project = ProjectFactory::create();

        $project->invite($newUser = factory(User::class)->create());

        $this->signIn($newUser);

        $this->post(action('ProjectTasksController@store',$project), $task = ['body' => 'footask']);

        $this->assertDatabaseHas('tasks',$task );
    }
}
