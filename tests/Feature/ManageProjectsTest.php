<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_guests_cannot_manage_projects(): void
    {
        $project = Project::factory()->create();

        $this->get('/projects')->assertRedirect('login');
        $this->get('/projects/create')->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
        $this->post('/projects', $project->toArray())->assertRedirect('login');
    }

    public function test_users_can_create_a_project(): void
    {
        $user = User::factory()->create();

        $this->signIn($user);

        $this->get('/projects/create')->assertStatus(200);
        
        $attributes = Project::factory()->raw(['owner_id' => $user->id]);

        $this->post('/projects', $attributes)->assertRedirect('/projects');

        $this->assertDatabaseHas('projects', $attributes);

        $this->get('/projects')->assertSee($attributes['title']);
    }

    public function test_users_can_view_their_project(): void
    {
        $user = User::factory()->create();

        $this->signIn($user);

        $project = Project::factory()->create(['owner_id' => $user->id]);

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    public function test_users_cannot_view_others_project(): void
    {
        $this->signIn();

        $project = Project::factory()->create();

        $this->get($project->path())->assertStatus(403);
    }

    public function test_project_requires_a_title(): void
    {
        $this->signIn();

        $attributes = Project::factory()->raw(['title' => '']);
        
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    public function test_project_requires_a_description(): void
    {
        $this->signIn();

        $attributes = Project::factory()->raw(['description' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }
}