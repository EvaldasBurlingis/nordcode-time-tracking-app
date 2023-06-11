<?php

namespace Tests\Feature\Task;

use App\Providers\RouteServiceProvider;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_new_task(): void
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Test Task',
            'comment' => 'Test Description',
            'date' => '2021-01-01',
            'time_spent' => '1'
        ]);

        $response->assertRedirect(route('tasks.index'));
    }

    public function test_user_can_download_task(): void
    {
        $user = UserFactory::new()->create();

        $user->tasks()->create([
            'title' => 'Test Task',
            'comment' => 'Test Description',
            'date' => '2021-01-01',
            'time_spent' => '1'
        ]);

        $response = $this->actingAs($user)->post(route('tasks.download',
            ['fileType' => 'csv', 'dateFrom' => '2021-01-01', 'dateTo' => '2021-01-01']));

        $response->assertStatus(200);
    }
}
