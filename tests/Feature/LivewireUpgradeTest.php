<?php

namespace Tests\Feature;

use App\Http\Livewire\BuatTautan;
use App\Http\Livewire\EditTautan;
use App\Models\Tautan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireUpgradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_application_pages_render(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['/dashboard', '/buat-tautan', '/kelola-tautan', '/user/profile'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_users_can_create_edit_and_view_a_public_tautan(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(BuatTautan::class)
            ->set('judul', 'Tautan Pengujian')
            ->set('slug', 'tautan-pengujian')
            ->call('saveToDatabase')
            ->assertHasNoErrors()
            ->assertDispatched('databaseSaved');

        $tautan = Tautan::where('user_id', $user->id)->sole();

        Livewire::test(EditTautan::class, ['id' => $tautan->id])
            ->set('judul', 'Tautan Diperbarui')
            ->call('updateToDatabase')
            ->assertHasNoErrors()
            ->assertDispatched('databaseSaved');

        $this->assertSame('Tautan Diperbarui', $tautan->fresh()->title);
        $this->get('/'.$tautan->slug)->assertOk()->assertSee('Tautan Diperbarui');
    }

    public function test_logo_upload_dispatches_preview_event_and_is_persisted(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $logo = UploadedFile::fake()->createWithContent('logo.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a7ioAAAAASUVORK5CYII='
        ));

        Livewire::test(BuatTautan::class)
            ->set('slug', 'tautan-dengan-logo')
            ->set('logoUpload', $logo)
            ->assertHasNoErrors()
            ->assertDispatched('logo-uploaded')
            ->call('saveToDatabase')
            ->assertHasNoErrors()
            ->assertDispatched('databaseSaved');

        $tautan = Tautan::sole();
        $this->assertTrue($tautan->use_uploaded_logo);
        Storage::disk('public')->assertExists('logos/'.$tautan->judul_gambar);
        $this->assertSame(Storage::disk('public')->url('logos/'.$tautan->judul_gambar), $tautan->logo_url);
    }

    public function test_user_management_page_remains_admin_only(): void
    {
        $this->actingAs(User::factory()->create())->get('/user-management')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/user-management')->assertOk();
    }

    public function test_link_pagination_query_string_still_renders(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/kelola-tautan?page=2')->assertOk();
    }

    public function test_livewire_rechecks_admin_role_on_later_requests(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/user-management')->assertOk();
        preg_match('/wire:snapshot="([^"]+)"/', $response->getContent(), $matches);
        $this->assertArrayHasKey(1, $matches);
        $snapshot = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
        $this->assertSame('user-management', json_decode($snapshot, true)['memo']['name']);

        $admin->update(['role' => 'user']);

        $this->actingAs($admin->fresh())->postJson('/livewire/update', [
            'components' => [[
                'snapshot' => $snapshot,
                'updates' => [
                    'createName' => 'Blocked User',
                    'createEmail' => 'blocked@example.test',
                    'createRole' => 'admin',
                    'createPassword' => 'a-long-test-password',
                    'createPasswordConfirmation' => 'a-long-test-password',
                ],
                'calls' => [['method' => 'createUser', 'params' => []]],
            ]],
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'blocked@example.test']);
    }
}
