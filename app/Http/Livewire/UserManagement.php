<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $sortBy = 'created_at'; // Default sort by creation date
    public $sortDirection = 'desc'; // Default: newest first
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showPasswordModal = false;

    // Loading states
    public $isLoading = false;
    public $loadingAction = '';
    public $loadingUserId = null;

    // Create form fields
    public $createName = '';
    public $createEmail = '';
    public $createRole = 'user';
    public $createPassword = '';
    public $createPasswordConfirmation = '';

    // Edit form fields
    public $editUserId = null;
    public $editName = '';
    public $editEmail = '';
    public $editRole = '';

    // Password reset fields
    public $passwordUserId = null;
    public $passwordUserName = '';
    public $resetPassword = '';
    public $resetPasswordConfirmation = '';

    
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->resetLoading();
    }

    public function hydrate()
    {
        $this->resetLoading();
    }

    /**
     * Check if current user can edit target user
     */
    private function canEditUser($targetUserId)
    {
        $currentUser = Auth::user();
        $targetUser = User::find($targetUserId);

        if (!$targetUser) {
            return false;
        }

        // Users cannot edit themselves (including admins)
        if ($targetUser->id === $currentUser->id) {
            return false;
        }

        // Admin cannot edit other admins
        if ($targetUser->role === 'admin') {
            return false;
        }

        return true;
    }

    /**
     * Get current authenticated user
     */
    private function getCurrentUser()
    {
        return Auth::user();
    }

    public function getFilteredUsersProperty()
    {
        return User::query()
            ->withCount('tautan') // Add count of tautan for each user
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->when($this->sortBy === 'role', function ($query) {
                // Sort: Admin first, then User
                $query->orderByRaw("CASE WHEN role = 'admin' THEN 1 ELSE 2 END");
            })
            ->when($this->sortBy === 'tautan_count', function ($query) {
                // Sort by tautan count
                $query->orderBy('tautan_count', $this->sortDirection === 'asc' ? 'asc' : 'desc');
            })
            ->when($this->sortBy === 'created_at', function ($query) {
                // Sort by creation date
                $query->orderBy($this->sortDirection === 'asc' ? 'created_at' : 'created_at', $this->sortDirection === 'asc' ? 'asc' : 'desc');
            })
            ->orderBy('created_at', 'desc') // Fallback to newest first
            ->paginate(10);
    }

    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function createUser()
    {
        $this->setLoading('create');

        $this->validate([
            'createName' => 'required|string|max:255',
            'createEmail' => 'required|email|unique:users,email|max:255',
            'createRole' => 'required|in:user,admin',
            'createPassword' => 'required|min:8|same:createPasswordConfirmation',
        ]);

        User::create([
            'name' => $this->createName,
            'email' => $this->createEmail,
            'role' => $this->createRole,
            'password' => Hash::make($this->createPassword),
        ]);

        $this->resetLoading();
        $this->closeModal();
        $this->resetCreateForm();
        $this->dispatch('notify', type: 'success', message: 'User created successfully');
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showPasswordModal = false;
        $this->resetLoading();
    }

    private function setLoading($action, $userId = null)
    {
        $this->isLoading = true;
        $this->loadingAction = $action;
        $this->loadingUserId = $userId;
    }

    private function resetLoading()
    {
        $this->isLoading = false;
        $this->loadingAction = '';
        $this->loadingUserId = null;
    }

    public function resetCreateForm()
    {
        $this->createName = '';
        $this->createEmail = '';
        $this->createRole = 'user';
        $this->createPassword = '';
        $this->createPasswordConfirmation = '';
    }

    public function resetEditForm()
    {
        $this->editUserId = null;
        $this->editName = '';
        $this->editEmail = '';
        $this->editRole = '';
    }

    public function resetPasswordForm()
    {
        $this->passwordUserId = null;
        $this->passwordUserName = '';
        $this->resetPassword = '';
        $this->resetPasswordConfirmation = '';
    }

    
    public function openEditModal($userId)
    {
        if (!$this->canEditUser($userId)) {
            $this->dispatch('notify', type: 'error', message: 'You cannot edit another admin user');
            return;
        }

        $user = User::findOrFail($userId);
        $this->editUserId = $user->id;
        $this->editName = $user->name; // Read-only display
        $this->editEmail = $user->email; // Read-only display
        $this->editRole = $user->role;
        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->setLoading('update', $this->editUserId);

        $this->validate([
            'editRole' => 'required|in:user,admin',
        ]);

        $user = User::findOrFail($this->editUserId);
        $currentUser = Auth::user();

        // Check if trying to edit another admin (including role change)
        if ($user->role === 'admin' && $user->id !== $currentUser->id) {
            $this->resetLoading();
            $this->dispatch('notify', type: 'error', message: 'You cannot edit another admin user');
            return;
        }

        // Additional check: prevent changing role to admin if current user is not admin (extra safety)
        if ($this->editRole === 'admin' && !$currentUser->isAdmin()) {
            $this->resetLoading();
            $this->dispatch('notify', type: 'error', message: 'You do not have permission to assign admin role');
            return;
        }

        // Only update role, don't change name or email
        $user->update([
            'role' => $this->editRole,
        ]);

        $this->resetLoading();
        $this->closeModal();
        $this->resetEditForm();
        $this->dispatch('notify', type: 'success', message: 'User role updated successfully');
    }

    public function openPasswordModal($userId)
    {
        if (!$this->canEditUser($userId)) {
            $this->dispatch('notify', type: 'error', message: 'You cannot reset password of another admin user');
            return;
        }

        $user = User::findOrFail($userId);
        $this->passwordUserId = $user->id;
        $this->passwordUserName = $user->name;
        $this->showPasswordModal = true;
    }

    public function resetUserPassword()
    {
        $this->setLoading('reset', $this->passwordUserId);

        $this->validate([
            'resetPassword' => 'required|min:8|same:resetPasswordConfirmation',
        ]);

        if (!$this->canEditUser($this->passwordUserId)) {
            $this->resetLoading();
            $this->dispatch('notify', type: 'error', message: 'You cannot reset password of another admin user');
            return;
        }

        $user = User::findOrFail($this->passwordUserId);
        $user->update([
            'password' => Hash::make($this->resetPassword),
        ]);

        $this->resetLoading();
        $this->closeModal();
        $this->resetPasswordForm();
        $this->dispatch('notify', type: 'success', message: 'Password reset successfully');
    }

    
    public function deactivateUser($userId)
    {
        $this->setLoading('deactivate', $userId);

        $user = User::findOrFail($userId);
        $currentUser = Auth::user();

        if ($user->id === $currentUser->id) {
            $this->resetLoading();
            $this->dispatch('notify', type: 'error', message: 'You cannot deactivate your own account');
            return;
        }

        // Check if trying to deactivate another admin
        if ($user->role === 'admin' && $user->id !== $currentUser->id) {
            $this->resetLoading();
            $this->dispatch('notify', type: 'error', message: 'You cannot deactivate another admin user');
            return;
        }

        // Update user status to inactive
        $user->update(['status' => 'inactive']);

        // Disable all user's tautan links
        $user->tautan()->update(['status' => 'inactive']);

        $this->resetLoading();
        $this->dispatch('notify', type: 'success', message: 'User deactivated successfully and all links disabled');
    }

    public function activateUser($userId)
    {
        $this->setLoading('activate', $userId);

        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) {
            $this->resetLoading();
            $this->dispatch('notify', type: 'error', message: 'You cannot activate your own account from this interface');
            return;
        }

        // Update user status to active
        $user->update(['status' => 'active']);

        // Re-enable all user's tautan links
        $user->tautan()->update(['status' => 'active']);

        $this->resetLoading();
        $this->dispatch('notify', type: 'success', message: 'User activated successfully and all links re-enabled');
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => $this->filteredUsers,
        ]);
    }

    /**
     * Check if current user can edit target user (for view usage)
     */
    public function getCanEditUserProperty($userId)
    {
        return $this->canEditUser($userId);
    }
}