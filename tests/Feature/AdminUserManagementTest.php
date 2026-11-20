<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use App\Support\UserRole;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_management_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->author()->create();

        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/users/create')->assertOk();
        $this->actingAs($admin)->get('/admin/users/'.$user->id.'/edit')->assertOk();
    }

    public function test_editor_cannot_view_user_management_pages(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
    }

    public function test_admin_can_create_a_verified_user_with_a_role_and_password(): void
    {
        $admin = User::factory()->admin()->create();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Content Editor',
                'email' => 'editor@example.com',
                'role' => UserRole::Editor->value,
                'password' => 'password123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::query()->where('email', 'editor@example.com')->firstOrFail();

        $this->assertSame(UserRole::Editor, $user->role);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_admin_can_update_a_user_without_resetting_their_password(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create(['password' => 'original-password']);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($admin);

        Livewire::test(EditUser::class, ['record' => $editor->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Editor',
                'email' => $editor->email,
                'role' => UserRole::Editor->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $editor->refresh();

        $this->assertSame('Updated Editor', $editor->name);
        $this->assertTrue(Hash::check('original-password', $editor->password));
    }

    public function test_only_admins_can_manage_users(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();
        $customer = User::factory()->customer()->create();

        $this->assertTrue($admin->can('viewAny', User::class));
        $this->assertFalse($editor->can('viewAny', User::class));
        $this->assertFalse($author->can('viewAny', User::class));
        $this->assertFalse($customer->can('viewAny', User::class));
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->author()->create();

        $this->assertFalse($admin->can('delete', $admin));
        $this->assertTrue($admin->can('delete', $otherUser));
    }
}
