<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccountStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_have_database_defaults_before_reloading(): void
    {
        $user = User::factory()->create();

        $this->assertSame('active', $user->status);
        $this->assertSame('user', $user->role);
    }

    public function test_inactive_and_disabled_users_cannot_confirm_passwords(): void
    {
        foreach (['inactive', 'disabled'] as $status) {
            $user = User::factory()->create(['status' => $status]);

            $this->actingAs($user)
                ->post('/user/confirm-password', ['password' => 'password'])
                ->assertRedirect(route('login'));

            $this->assertGuest();
        }
    }
}
