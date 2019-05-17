<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_project()
    {
        $user = factory('App\User')->create();

        $this->assertInstanceOf(Collection::class, $user->projects);
    }

    /** @test */
    public function a_user_has_accessible_project()
    {
        $john = $this->signIn();

        $project = ProjectFactory::ownedBy($john)->create();

        $this->assertCount(1,$john->accessibleProjects());

        $sally = factory(User::class)->create();
        $nick = factory(User::class)->create();

        $project->invite($nick);

        $this->assertCount(0,$sally->accessibleProjects());

        $project->invite($sally);

        $this->assertCount(1,$sally->accessibleProjects());
    }
}
