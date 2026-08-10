<?php

namespace Tests\Feature;

use App\Models\Mess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_zero_percentage_when_no_deposits_exist(): void
    {
        $mess = Mess::create([
            'name' => 'Test Mess',
            'address' => 'Test Address',
        ]);

        $user = User::factory()->create([
            'slug' => 'test-user',
        ]);
        $user->current_mess_id = $mess->id;
        $user->save();

        $response = $this->actingAs($user, 'api')->getJson('/api/mess/transactions');

        $response->assertOk();
        $response->assertJsonPath('data.totalDeposits', 0);
        $response->assertJsonPath('data.totalExpenses', 0);
        $response->assertJsonPath('data.totalUsedPercentage', '0.00');
    }
}
