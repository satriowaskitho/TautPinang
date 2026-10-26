<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tautan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KelolaTautan extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortDir = 'desc';
    public $perPage = 10;

    // Filter properties
    public $filterStatus = 'all'; // all, active, inactive
    public $filterImageType = 'all'; // all, uploaded, url

    // Modal states
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    public $selectedTautan = null;

    // Bulk actions
    public $selectAll = false;
    public $selectedItems = [];
    public $showBulkActions = false;
    public $bulkAction = ''; // delete, activate, deactivate

    // Import
    public $showImportModal = false;
    public $importFile = null;

    // Edit form
    public $editTitle = '';
    public $editDescription = '';
    public $editIsActive = true;

    // Loading states
    public $isLoading = false;

    // Deleted/Recovery states
    public $showDeletedModal = false;
    public $showForceDeleteModal = false;
    public $showHistoryTab = false;
    public $selectedDeletedTautan = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDir' => ['except' => 'desc'],
        'filterStatus' => ['except' => 'all'],
        'filterImageType' => ['except' => 'all'],
        'showHistoryTab' => ['except' => false],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterImageType()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->getTautanIds();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        $this->selectAll = count($this->selectedItems) === $this->getTautanIds()->count();
    }

    private function getTautanIds()
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'admin';

        if ($isAdmin) {
            // Admin: Bisa manage semua tautan
            return Tautan::when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('slug', 'like', '%' . $this->search . '%')
                          ->orWhereHas('user', function ($userQuery) {
                              $userQuery->where('name', 'like', '%' . $this->search . '%')
                                       ->orWhere('email', 'like', '%' . $this->search . '%');
                          });
                    });
                })
                ->when($this->filterStatus !== 'all', function ($query) {
                    if ($this->filterStatus === 'active') {
                        $query->where('is_active', true);
                    } elseif ($this->filterStatus === 'inactive') {
                        $query->where('is_active', false);
                    }
                })
                ->when($this->filterImageType !== 'all', function ($query) {
                    if ($this->filterImageType === 'uploaded') {
                        $query->where('use_uploaded_logo', true);
                    } elseif ($this->filterImageType === 'url') {
                        $query->where('use_uploaded_logo', false);
                    }
                })
                ->pluck('id');
        } else {
            // User biasa: Hanya tautan sendiri
            return Tautan::byUser(Auth::id())
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('slug', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->filterStatus !== 'all', function ($query) {
                    if ($this->filterStatus === 'active') {
                        $query->where('is_active', true);
                    } elseif ($this->filterStatus === 'inactive') {
                        $query->where('is_active', false);
                    }
                })
                ->when($this->filterImageType !== 'all', function ($query) {
                    if ($this->filterImageType === 'uploaded') {
                        $query->where('use_uploaded_logo', true);
                    } elseif ($this->filterImageType === 'url') {
                        $query->where('use_uploaded_logo', false);
                    }
                })
                ->pluck('id');
        }
    }

    public function resetFilters()
    {
        $this->filterStatus = 'all';
        $this->filterImageType = 'all';
        $this->search = '';
        $this->selectedItems = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function sortByField($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'asc';
        }
    }

    public function showDeleteConfirm($tautanId)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'admin';

        $this->selectedTautan = Tautan::find($tautanId);
        if ($this->selectedTautan && ($isAdmin || $this->selectedTautan->user_id === Auth::id())) {
            $this->showDeleteModal = true;
        }
    }

    public function deleteConfirmed()
    {
        try {
            $this->isLoading = true;

            $currentUser = Auth::user();
            $isAdmin = $currentUser->role === 'admin';

            if ($this->selectedTautan && ($isAdmin || $this->selectedTautan->user_id === Auth::id())) {
                $this->selectedTautan->delete();

                $this->showDeleteModal = false;
                $this->selectedTautan = null;

                $this->dispatch('tautanDeleted');
                session()->flash('success', 'Tautan berhasil dihapus!');
            } else {
                session()->flash('error', 'Tautan tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus tautan: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function showEdit($tautanId)
    {
        $this->selectedTautan = Tautan::find($tautanId);
        if ($this->selectedTautan && $this->selectedTautan->user_id === Auth::id()) {
            $this->editTitle = $this->selectedTautan->title;
            $this->editDescription = $this->selectedTautan->description;
            $this->editIsActive = $this->selectedTautan->is_active;
            $this->showEditModal = true;
        } elseif ($this->selectedTautan) {
            session()->flash('error', 'Hanya pembuat tautan yang dapat mengedit konten ini. Admin dapat mengaktifkan/menonaktifkan tautan.');
        }
    }

    public function updateTautan()
    {
        try {
            $this->isLoading = true;

            $this->validate([
                'editTitle' => 'required|string|max:255',
                'editDescription' => 'nullable|string|max:1000',
                'editIsActive' => 'boolean',
            ]);

            if ($this->selectedTautan && $this->selectedTautan->user_id === Auth::id()) {
                $this->selectedTautan->update([
                    'title' => $this->editTitle,
                    'description' => $this->editDescription,
                    'is_active' => $this->editIsActive,
                ]);

                $this->showEditModal = false;
                $this->selectedTautan = null;

                $this->dispatch('tautanUpdated');
                session()->flash('success', 'Tautan berhasil diperbarui!');
            } else {
                session()->flash('error', 'Hanya pembuat tautan yang dapat mengedit konten ini. Admin dapat mengaktifkan/menonaktifkan tautan.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memperbarui tautan: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function showView($tautanId)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'admin';

        $this->selectedTautan = Tautan::with('user')->find($tautanId);
        if ($this->selectedTautan && ($isAdmin || $this->selectedTautan->user_id === Auth::id())) {
            $this->showViewModal = true;
        }
    }

    public function toggleActive($tautanId)
    {
        try {
            $currentUser = Auth::user();
            $isAdmin = $currentUser->role === 'admin';

            $tautan = Tautan::find($tautanId);
            if ($tautan) {
                // Check if user has permission to toggle this tautan
                if ($isAdmin || $tautan->user_id === Auth::id()) {
                    $tautan->is_active = !$tautan->is_active;
                    $tautan->save();

                    // Debug: Log the update
                    \Log::info('Tautan toggled', [
                        'id' => $tautan->id,
                        'title' => $tautan->title,
                        'new_is_active' => $tautan->is_active,
                        'user_id' => $tautan->user_id,
                        'current_user_id' => Auth::id(),
                        'is_admin' => $isAdmin
                    ]);

                    $status = $tautan->is_active ? 'diaktifkan' : 'dinonaktifkan';
                    session()->flash('success', "Tautan berhasil {$status}!");
                } else {
                    session()->flash('error', 'Anda tidak memiliki izin untuk mengubah tautan ini.');
                }
            } else {
                session()->flash('error', 'Tautan tidak ditemukan.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengubah status tautan: ' . $e->getMessage());
        }
    }

    public function duplicateTautan($tautanId)
    {
        try {
            $originalTautan = Tautan::find($tautanId);
            if ($originalTautan && $originalTautan->user_id === Auth::id()) {
                $newSlug = $originalTautan->slug . '-copy-' . time();

                Tautan::create([
                    'user_id' => Auth::id(),
                    'title' => $originalTautan->title . ' (Copy)',
                    'description' => $originalTautan->description,
                    'slug' => $newSlug,
                    'links' => $originalTautan->links,
                    'styles' => $originalTautan->styles,
                    'logo_url' => $originalTautan->logo_url,
                    'footer_text_1' => $originalTautan->footer_text_1,
                    'footer_text_2' => $originalTautan->footer_text_2,
                    'is_active' => false, // Mulai dalam status non-aktif
                    'tujuan' => $originalTautan->tujuan ?? 'external',
                    'use_uploaded_logo' => $originalTautan->use_uploaded_logo ?? false,
                    'judul_gambar' => $originalTautan->judul_gambar,
                ]);

                session()->flash('success', 'Tautan berhasil diduplikasi!');
            } else {
                session()->flash('error', 'Tautan tidak ditemukan atau Anda tidak memiliki izin untuk menduplikasinya.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menduplikasi tautan: ' . $e->getMessage());
        }
    }

    // Bulk action methods
    public function showBulkActionConfirm($action)
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Pilih minimal satu tautan untuk melakukan aksi ini.');
            return;
        }

        $this->bulkAction = $action;
        $this->showBulkActions = true;
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedItems) || empty($this->bulkAction)) {
            return;
        }

        $count = 0;
        $action = $this->bulkAction;
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'admin';

        // Debug: Log the operation
        \Log::info('Bulk action started', [
            'action' => $action,
            'selected_items' => $this->selectedItems,
            'is_admin' => $isAdmin,
            'user_id' => Auth::id()
        ]);

        if ($isAdmin) {
            // Admin: Bisa manage semua tautan
            $tautans = Tautan::whereIn('id', $this->selectedItems)->get();
        } else {
            // User biasa: Hanya tautan sendiri
            $tautans = Tautan::byUser(Auth::id())->whereIn('id', $this->selectedItems)->get();
        }

        // Debug: Log what we found
        \Log::info('Tautan found for bulk action', [
            'count' => $tautans->count(),
            'tautan_ids' => $tautans->pluck('id')->toArray(),
            'tautan_titles' => $tautans->pluck('title')->toArray()
        ]);

        foreach ($tautans as $tautan) {
            try {
                switch ($action) {
                    case 'delete':
                        $tautan->delete();
                        $count++;
                        break;
                    case 'activate':
                        $tautan->is_active = true;
                        $tautan->save();
                        $count++;

                        // Debug: Log the update
                        \Log::info('Tautan activated', [
                            'id' => $tautan->id,
                            'title' => $tautan->title,
                            'is_active' => $tautan->is_active,
                            'user_id' => $tautan->user_id
                        ]);
                        break;
                    case 'deactivate':
                        $tautan->is_active = false;
                        $tautan->save();
                        $count++;

                        // Debug: Log the update
                        \Log::info('Tautan deactivated', [
                            'id' => $tautan->id,
                            'title' => $tautan->title,
                            'is_active' => $tautan->is_active,
                            'user_id' => $tautan->user_id
                        ]);
                        break;
                }
            } catch (\Exception $e) {
                \Log::error('Error updating tautan', [
                    'id' => $tautan->id,
                    'action' => $action,
                    'error' => $e->getMessage()
                ]);
                session()->flash('error', 'Terjadi kesalahan pada tautan ' . $tautan->title . ': ' . $e->getMessage());
            }
        }

        $this->selectedItems = [];
        $this->selectAll = false;
        $this->showBulkActions = false;
        $this->bulkAction = '';

        $message = match($action) {
            'delete' => "$count tautan berhasil dihapus!",
            'activate' => "$count tautan berhasil diaktifkan!",
            'deactivate' => "$count tautan berhasil dinonaktifkan!",
            default => "Aksi bulk berhasil dilakukan!"
        };

        session()->flash('success', $message);
    }

    public function cancelBulkAction()
    {
        $this->showBulkActions = false;
        $this->bulkAction = '';
    }

    // Import methods
    public function showImportModal()
    {
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
    }

    public function importData()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:json|max:1024', // Max 1MB
        ]);

        try {
            $content = file_get_contents($this->importFile->getRealPath());
            $data = json_decode($content, true);

            if (!$data || !is_array($data)) {
                session()->flash('error', 'File JSON tidak valid atau kosong.');
                return;
            }

            $importedCount = 0;
            $skippedCount = 0;

            foreach ($data as $item) {
                // Validasi data
                if (empty($item['title']) || empty($item['slug'])) {
                    $skippedCount++;
                    continue;
                }

                // Cek apakah slug sudah ada
                $existingSlug = Tautan::byUser(Auth::id())->where('slug', $item['slug'])->first();
                if ($existingSlug) {
                    // Generate slug baru jika sudah ada
                    $item['slug'] = $item['slug'] . '-' . time() . '-' . rand(100, 999);
                }

                Tautan::create([
                    'user_id' => Auth::id(),
                    'title' => $item['title'],
                    'description' => $item['description'] ?? null,
                    'slug' => $item['slug'],
                    'links' => $item['links'] ?? [],
                    'styles' => $item['styles'] ?? [],
                    'logo_url' => $item['logo_url'] ?? null,
                    'footer_text_1' => $item['footer_text_1'] ?? null,
                    'footer_text_2' => $item['footer_text_2'] ?? null,
                    'is_active' => $item['is_active'] ?? true,
                    'tujuan' => $item['tujuan'] ?? 'external',
                    'use_uploaded_logo' => $item['use_uploaded_logo'] ?? false,
                    'judul_gambar' => $item['judul_gambar'] ?? null,
                ]);

                $importedCount++;
            }

            $this->closeImportModal();
            session()->flash('success', "Berhasil mengimport {$importedCount} tautan." . ($skippedCount > 0 ? " {$skippedCount} data dilewati karena tidak valid." : ""));

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengimport file: ' . $e->getMessage());
        }
    }

    public function exportData()
    {
        $tautans = Tautan::byUser(Auth::id())->get();

        $filename = 'tautan-export-' . date('Y-m-d-H-i-s') . '.json';
        $data = $tautans->map(function ($tautan) {
            return [
                'title' => $tautan->title,
                'description' => $tautan->description,
                'slug' => $tautan->slug,
                'links' => $tautan->links,
                'styles' => $tautan->styles,
                'logo_url' => $tautan->logo_url,
                'footer_text_1' => $tautan->footer_text_1,
                'footer_text_2' => $tautan->footer_text_2,
                'is_active' => $tautan->is_active,
                'created_at' => $tautan->created_at,
            ];
        });

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function closeModals()
    {
        $this->showDeleteModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->showDeletedModal = false;
        $this->showForceDeleteModal = false;
        $this->selectedTautan = null;
        $this->selectedDeletedTautan = null;
        $this->reset(['editTitle', 'editDescription', 'editIsActive']);
    }

    // Recovery methods
    public function showDeletedHistory()
    {
        $this->showHistoryTab = true;
    }

    public function hideDeletedHistory()
    {
        $this->showHistoryTab = false;
    }

    public function showRecoverConfirm($tautanId)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'admin';

        $this->selectedDeletedTautan = Tautan::withTrashed()->find($tautanId);
        if ($this->selectedDeletedTautan &&
            ($isAdmin || $this->selectedDeletedTautan->user_id === Auth::id()) &&
            $this->selectedDeletedTautan->deleted_at) {
            $this->showDeletedModal = true;
        }
    }

    public function recoverTautan()
    {
        try {
            $this->isLoading = true;

            $currentUser = Auth::user();
            $isAdmin = $currentUser->role === 'admin';

            if ($this->selectedDeletedTautan &&
                ($isAdmin || $this->selectedDeletedTautan->user_id === Auth::id()) &&
                $this->selectedDeletedTautan->deleted_at) {

                $this->selectedDeletedTautan->restore();

                $this->showDeletedModal = false;
                $this->selectedDeletedTautan = null;

                $this->dispatch('tautanRecovered');
                session()->flash('success', 'Tautan berhasil dipulihkan!');
            } else {
                session()->flash('error', 'Tautan tidak ditemukan atau Anda tidak memiliki izin untuk memulihkannya.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memulihkan tautan: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function forceDeleteConfirm($tautanId)
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'admin';

        $this->selectedDeletedTautan = Tautan::withTrashed()->find($tautanId);
        if ($this->selectedDeletedTautan &&
            ($isAdmin || $this->selectedDeletedTautan->user_id === Auth::id()) &&
            $this->selectedDeletedTautan->deleted_at) {
            $this->showForceDeleteModal = true;
        }
    }

    public function forceDeleteTautan()
    {
        try {
            $this->isLoading = true;

            $currentUser = Auth::user();
            $isAdmin = $currentUser->role === 'admin';

            if ($this->selectedDeletedTautan &&
                ($isAdmin || $this->selectedDeletedTautan->user_id === Auth::id()) &&
                $this->selectedDeletedTautan->deleted_at) {

                // Delete associated uploaded logo if exists
                if ($this->selectedDeletedTautan->use_uploaded_logo && !empty($this->selectedDeletedTautan->judul_gambar)) {
                    $logoPath = 'logos/' . $this->selectedDeletedTautan->judul_gambar;
                    if (Storage::disk('public')->exists($logoPath)) {
                        Storage::disk('public')->delete($logoPath);
                    }
                }

                // Force delete from database
                $this->selectedDeletedTautan->forceDelete();

                $this->showForceDeleteModal = false;
                $this->selectedDeletedTautan = null;

                $this->dispatch('tautanForceDeleted');
                session()->flash('success', 'Tautan berhasil dihapus permanen!');
            } else {
                session()->flash('error', 'Tautan tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus permanen tautan: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function recoverMultiple()
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Pilih minimal satu tautan untuk dipulihkan.');
            return;
        }

        try {
            $count = 0;
            $currentUser = Auth::user();
            $isAdmin = $currentUser->role === 'admin';

            if ($isAdmin) {
                // Admin: Bisa recover semua tautan
                $deletedTautans = Tautan::onlyTrashed()
                    ->whereIn('id', $this->selectedItems)
                    ->get();
            } else {
                // User biasa: Hanya tautan sendiri
                $deletedTautans = Tautan::byUser(Auth::id())
                    ->onlyTrashed()
                    ->whereIn('id', $this->selectedItems)
                    ->get();
            }

            foreach ($deletedTautans as $tautan) {
                $tautan->restore();
                $count++;
            }

            $this->selectedItems = [];
            $this->selectAll = false;

            session()->flash('success', "{$count} tautan berhasil dipulihkan!");
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memulihkan tautan: ' . $e->getMessage());
        }
    }

    public function forceDeleteMultiple()
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Pilih minimal satu tautan untuk dihapus permanen.');
            return;
        }

        try {
            $count = 0;
            $currentUser = Auth::user();
            $isAdmin = $currentUser->role === 'admin';

            if ($isAdmin) {
                // Admin: Bisa force delete semua tautan
                $deletedTautans = Tautan::onlyTrashed()
                    ->whereIn('id', $this->selectedItems)
                    ->get();
            } else {
                // User biasa: Hanya tautan sendiri
                $deletedTautans = Tautan::byUser(Auth::id())
                    ->onlyTrashed()
                    ->whereIn('id', $this->selectedItems)
                    ->get();
            }

            foreach ($deletedTautans as $tautan) {
                // Delete associated uploaded logo if exists
                if ($tautan->use_uploaded_logo && !empty($tautan->judul_gambar)) {
                    $logoPath = 'logos/' . $tautan->judul_gambar;
                    if (Storage::disk('public')->exists($logoPath)) {
                        Storage::disk('public')->delete($logoPath);
                    }
                }

                $tautan->forceDelete();
                $count++;
            }

            $this->selectedItems = [];
            $this->selectAll = false;

            session()->flash('success', "{$count} tautan berhasil dihapus permanen!");
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus permanen tautan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'admin';

        // Get active tautans (not deleted)
        if ($isAdmin) {
            // Admin: Lihat semua tautan dengan nama pembuat
            $query = Tautan::with('user')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('slug', 'like', '%' . $this->search . '%')
                          ->orWhereHas('user', function ($userQuery) {
                              $userQuery->where('name', 'like', '%' . $this->search . '%')
                                       ->orWhere('email', 'like', '%' . $this->search . '%');
                          });
                    });
                })
                ->when($this->filterStatus !== 'all', function ($query) {
                    if ($this->filterStatus === 'active') {
                        $query->where('is_active', true);
                    } elseif ($this->filterStatus === 'inactive') {
                        $query->where('is_active', false);
                    }
                })
                ->when($this->filterImageType !== 'all', function ($query) {
                    if ($this->filterImageType === 'uploaded') {
                        $query->where('use_uploaded_logo', true);
                    } elseif ($this->filterImageType === 'url') {
                        $query->where('use_uploaded_logo', false);
                    }
                });
        } else {
            // User biasa: Lihat tautan sendiri saja
            $query = Tautan::byUser(Auth::id())
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('slug', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->filterStatus !== 'all', function ($query) {
                    if ($this->filterStatus === 'active') {
                        $query->where('is_active', true);
                    } elseif ($this->filterStatus === 'inactive') {
                        $query->where('is_active', false);
                    }
                })
                ->when($this->filterImageType !== 'all', function ($query) {
                    if ($this->filterImageType === 'uploaded') {
                        $query->where('use_uploaded_logo', true);
                    } elseif ($this->filterImageType === 'url') {
                        $query->where('use_uploaded_logo', false);
                    }
                });
        }

        $tautans = $query->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        // Get deleted tautans for history
        if ($isAdmin) {
            $deletedTautans = Tautan::with('user')
                ->onlyTrashed()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('slug', 'like', '%' . $this->search . '%')
                          ->orWhereHas('user', function ($userQuery) {
                              $userQuery->where('name', 'like', '%' . $this->search . '%')
                                       ->orWhere('email', 'like', '%' . $this->search . '%');
                          });
                    });
                })
                ->orderBy('deleted_at', 'desc')
                ->get();
        } else {
            $deletedTautans = Tautan::byUser(Auth::id())
                ->onlyTrashed()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('slug', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('deleted_at', 'desc')
                ->get();
        }

        // Statistics
        if ($isAdmin) {
            $totalTautans = Tautan::count();
            $activeTautans = Tautan::where('is_active', true)->count();
            $inactiveTautans = Tautan::where('is_active', false)->count();
            $deletedTautansCount = Tautan::onlyTrashed()->count();
            $uploadedImageTautans = Tautan::where('use_uploaded_logo', true)->count();
            $totalLinks = Tautan::get()->sum(function ($tautan) {
                return is_array($tautan->links) ? count($tautan->links) : 0;
            });
        } else {
            $totalTautans = Tautan::byUser(Auth::id())->count();
            $activeTautans = Tautan::byUser(Auth::id())->where('is_active', true)->count();
            $inactiveTautans = Tautan::byUser(Auth::id())->where('is_active', false)->count();
            $deletedTautansCount = Tautan::byUser(Auth::id())->onlyTrashed()->count();
            $uploadedImageTautans = Tautan::byUser(Auth::id())->where('use_uploaded_logo', true)->count();
            $totalLinks = Tautan::byUser(Auth::id())->get()->sum(function ($tautan) {
                return is_array($tautan->links) ? count($tautan->links) : 0;
            });
        }

        $statistics = [
            'total' => $totalTautans,
            'active' => $activeTautans,
            'inactive' => $inactiveTautans,
            'deleted' => $deletedTautansCount,
            'uploaded_images' => $uploadedImageTautans,
            'total_links' => $totalLinks,
            'filtered_count' => $tautans->total(),
            'is_admin' => $isAdmin,
        ];

        return view('livewire.kelola-tautan', compact('tautans', 'deletedTautans', 'statistics'));
    }
}
