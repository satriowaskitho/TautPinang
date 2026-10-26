<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Buat Tautan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Pesan Sukses -->
            @if (session()->has('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-400 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Pesan Error -->
            @if (session()->has('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-400 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- ✅ INFO: Preview URL Laravel Internal -->
            @if (session()->has('public_url'))
                <div class="p-4 mb-4 border border-blue-400 rounded-lg bg-blue-50">
                    <p class="mb-2 text-sm font-semibold text-blue-800">✅ Tautan berhasil dibuat!</p>
                    <p class="text-sm text-blue-700">Akses di:
                        <a href="{{ session('public_url') }}" target="_blank" class="font-mono text-blue-900 underline">
                            {{ session('public_url') }}
                        </a>
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Bagian Form -->
                <div class="bg-white shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">
                            ✏️ Buat Tautan Baru
                            <span class="ml-2 text-sm font-normal text-gray-500">Lihat Preview →</span>
                        </h3>

                        <!-- ✅ INFO: Laravel Internal System -->
                        <div class="p-4 mb-6 border border-blue-200 rounded-lg bg-blue-50">
                            <p class="mb-2 text-sm text-blue-700">
                                <strong>🚀 Sistem Internal Laravel</strong>
                            </p>
                            <p class="text-xs text-blue-600">Tautan akan langsung bisa diakses setelah disimpan</p>
                            @if (!empty($this->slug))
                                <div class="p-3 mt-3 bg-blue-100 rounded-lg">
                                    <div class="mb-1 text-xs text-blue-600">Preview URL:</div>
                                    <a href="{{ route('tautan.show', $this->slug) }}" target="_blank"
                                        class="font-mono text-sm text-blue-800 break-all hover:text-blue-900">
                                        {{ route('tautan.show', $this->slug) }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Logo/Avatar Section -->
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Logo/Avatar</label>

                            <!-- Toggle Switch untuk URL vs Upload -->
                            <div
                                class="p-4 mb-4 border border-gray-300 rounded-lg {{ $hasUploadedImage ? 'bg-gray-100' : 'bg-gray-50' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center">
                                            <input type="radio" id="logoUrl" wire:model.live="useUploadedLogo"
                                                value="false"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                            <label for="logoUrl" class="ml-2 text-sm font-medium text-gray-900">🔗 Link
                                                URL</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="radio" id="logoUpload" wire:model.live="useUploadedLogo"
                                                value="true"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                            <label for="logoUpload" class="ml-2 text-sm font-medium text-gray-900">📁
                                                Upload Gambar</label>
                                        </div>
                                    </div>

                                    <!-- Mini Preview -->
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">Preview:</span>
                                        <div
                                            class="relative w-8 h-8 overflow-hidden bg-gray-100 border border-gray-300 rounded-lg">
                                            <img id="miniPreview" src="{{ $this->getCurrentLogoSource() }}"
                                                alt="Logo Preview"
                                                class="object-cover w-full h-full transition-opacity duration-200"
                                                onload="this.style.opacity='1'"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                            <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400 bg-gray-100"
                                                style="display:none;">❌</div>
                                        </div>
                                    </div>
                                </div>

                                @if ($hasUploadedImage)
                                    <div class="p-3 mt-3 border border-yellow-200 rounded-lg bg-yellow-50">
                                        <div class="flex items-start space-x-2">
                                            <svg class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-yellow-800">Mode Upload Aktif</p>
                                                <p class="text-xs text-yellow-700">Klik "❌ Hapus" di bawah untuk beralih
                                                    ke mode URL.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- URL Input Section -->
                            @if (!$useUploadedLogo)
                                <div class="mb-4">
                                    <label class="block mb-1 text-sm font-medium text-gray-600">Link URL Gambar</label>
                                    <input type="text" wire:model.live.debounce.500ms="logoUrl"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="https://example.com/logo.jpg">
                                    @error('logoUrl')
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror

                                    @if (!empty($logoUrl))
                                        <div class="p-3 mt-2 border border-gray-200 rounded-lg bg-gray-50">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="relative w-16 h-16 overflow-hidden bg-white border border-gray-300 rounded-lg">
                                                    <img src="{{ $logoUrl }}" alt="URL Preview"
                                                        class="object-cover w-full h-full"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400 bg-red-50"
                                                        style="display:none;">❌ Gagal load</div>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-xs font-medium text-gray-600">Preview URL</p>
                                                    <p class="text-xs text-gray-500 break-all">
                                                        {{ Str::limit($logoUrl, 60) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Upload Section -->
                            @if ($useUploadedLogo || $hasUploadedImage)
                                <div class="mb-4">
                                    <label class="block mb-1 text-sm font-medium text-gray-600">
                                        Upload File Gambar
                                        <span class="text-xs text-gray-400">(Max: 2MB, Format: JPG, PNG, GIF,
                                            WEBP)</span>
                                    </label>

                                    @if (!$hasUploadedImage)
                                        <div class="relative">
                                            <input type="file" wire:model.live="logoUpload" accept="image/*"
                                                class="absolute inset-0 z-10 w-full h-full opacity-0 cursor-pointer"
                                                id="logoUploadInput">
                                            <div
                                                class="p-6 text-center transition-colors border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 hover:bg-blue-50">
                                                <svg class="w-12 h-12 mx-auto text-gray-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <div class="text-sm text-gray-600">
                                                    <span class="font-medium text-blue-600 hover:text-blue-500">Klik
                                                        untuk upload</span>
                                                    atau drag & drop gambar di sini
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP hingga 2MB</p>
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="p-6 text-center bg-gray-100 border-2 border-gray-300 border-dashed rounded-lg">
                                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                </path>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-600">Upload Area Terkunci</p>
                                            <p class="text-xs text-gray-500">Hapus preview di bawah untuk upload ulang.
                                            </p>
                                        </div>
                                    @endif

                                    @error('logoUpload')
                                        <span class="block mt-1 text-sm text-red-500">{{ $message }}</span>
                                    @enderror

                                    <div wire:loading wire:target="logoUpload" class="mt-2">
                                        <div class="flex items-center space-x-2 text-blue-600">
                                            <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span class="text-sm">Mengupload...</span>
                                        </div>
                                    </div>

                                    @if ($hasUploadedImage && ($logoUpload || $logoPreviewUrl))
                                        <div class="p-4 mt-3 border-2 border-green-300 rounded-lg bg-green-50">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="relative w-16 h-16 overflow-hidden bg-white border-2 border-green-300 rounded-lg">
                                                    @if ($logoPreviewUrl)
                                                        <img src="{{ $logoPreviewUrl }}" alt="Upload Preview"
                                                            class="object-cover w-full h-full">
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-green-800">✅ Gambar berhasil
                                                        diupload</p>
                                                    @if ($logoUpload)
                                                        <p class="text-xs text-green-600">
                                                            {{ $logoUpload->getClientOriginalName() }}
                                                            ({{ round($logoUpload->getSize() / 1024, 1) }} KB)
                                                        </p>
                                                    @endif
                                                </div>
                                                <button type="button" wire:click="clearUploadedLogo"
                                                    class="flex items-center justify-center w-8 h-8 text-red-500 bg-white border-2 border-red-300 rounded-full hover:bg-red-50"
                                                    title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Judul -->
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Judul Halaman <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.live.debounce.300ms="judul"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="BPS Kota Tanjungpinang">
                            @error('judul')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Slug/Nama File -->
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Slug URL <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(yoursite.com/<strong>slug-ini</strong>)</span>
                            </label>
                            <input type="text" wire:model.live.debounce.300ms="slug"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="bps-tanjungpinang" pattern="[a-zA-Z0-9\-_]+">
                            @error('slug')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                            <div class="mt-1 text-xs text-gray-500">Format: huruf, angka, tanda hubung (-) dan garis
                                bawah (_)</div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                            <textarea wire:model.live.debounce.300ms="deskripsi" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Badan Pusat Statistik Kota Tanjungpinang"></textarea>
                            @error('deskripsi')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Daftar Link -->
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Daftar Tautan <span class="text-red-500">*</span>
                            </label>

                            <div id="links-container" class="space-y-3">
                            @foreach ($links as $index => $link)
                                <div class="link-item p-3 mb-3 rounded-lg bg-gray-50 cursor-move hover:bg-gray-100 transition-colors" wire:key="link-{{ $index }}" data-index="{{ $index }}" draggable="true">
                                    <div class="flex items-start gap-2">
                                        <!-- Drag Handle -->
                                        <div class="flex items-center cursor-grab active:cursor-grabbing pt-2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <input type="text"
                                                wire:model.live.debounce.500ms="links.{{ $index }}.judul"
                                                class="w-full px-3 py-2 mb-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="Judul Tautan (contoh: 🌐 Website Resmi)">

                                            <input type="url"
                                                wire:model.live.debounce.500ms="links.{{ $index }}.url"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                placeholder="https://example.com">

                                            <!-- Toggle untuk Custom Styling -->
                                            <div
                                                class="flex items-center gap-2 p-2 mt-3 border border-blue-200 rounded-lg bg-blue-50">
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="checkbox"
                                                        wire:model.live.debounce.500ms="links.{{ $index }}.enableCustomStyling"
                                                        class="sr-only peer">
                                                    <div
                                                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                                    </div>
                                                    <span class="ml-3 text-sm font-medium text-gray-700">Gunakan Style
                                                        Kustom</span>
                                                </label>
                                            </div>

                                            <!-- Custom Colors for this Link (Hanya muncul jika enableCustomStyling = true) -->
                                            @if ($link['enableCustomStyling'] ?? false)
                                                <div class="flex gap-2 mt-2">
                                                    <div class="flex-1">
                                                        <label
                                                            class="block mb-1 text-xs font-medium text-gray-600">Warna
                                                            Background</label>
                                                        <div class="flex items-center gap-2">
                                                            <input type="color"
                                                                wire:model.live.debounce.500ms="links.{{ $index }}.backgroundColor"
                                                                class="h-8 border border-gray-300 rounded cursor-pointer"
                                                                value="{{ $link['backgroundColor'] ?? '#FFFFFF' }}">
                                                            <input type="text"
                                                                wire:model.live.debounce.500ms="links.{{ $index }}.backgroundColor"
                                                                class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded"
                                                                placeholder="#FFFFFF">
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <label
                                                            class="block mb-1 text-xs font-medium text-gray-600">Warna
                                                            Teks</label>
                                                        <div class="flex items-center gap-2">
                                                            <input type="color"
                                                                wire:model.live.debounce.500ms="links.{{ $index }}.textColor"
                                                                class="h-8 border border-gray-300 rounded cursor-pointer"
                                                                value="{{ $link['textColor'] ?? '#002366' }}">
                                                            <input type="text"
                                                                wire:model.live.debounce.500ms="links.{{ $index }}.textColor"
                                                                class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded"
                                                                placeholder="#002366">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Icon & Active Toggle (Inline) -->
                                            <div class="flex items-center justify-between gap-2 mt-3">
                                                <!-- Icon Picker -->
                                                <div class="flex items-center gap-2">
                                                    @if(!empty($link['icon']))
                                                        <button type="button" wire:click="selectIcon({{ $index }})"
                                                            class="px-3 py-2 bg-purple-100 text-purple-700 text-sm rounded-lg hover:bg-purple-200 transition-colors border border-purple-300">
                                                            <i class="{{ $link['icon'] }} mr-1"></i> Ganti
                                                        </button>
                                                        <button type="button" wire:click="removeIcon({{ $index }})"
                                                            class="px-3 py-2 bg-red-100 text-red-700 text-sm rounded-lg hover:bg-red-200 transition-colors border border-red-300">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" wire:click="selectIcon({{ $index }})"
                                                            class="px-3 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">
                                                            <i class="fas fa-icons mr-1"></i> Icon
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Active Toggle Switch -->
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="checkbox"
                                                        wire:model.live.debounce.500ms="links.{{ $index }}.active"
                                                        class="sr-only peer">
                                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-700">Aktif</span>
                                                </label>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="removeLink({{ $index }})"
                                            class="p-2 text-red-500 transition rounded-lg hover:bg-red-50">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('links.' . $index . '.judul')
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                    @error('links.' . $index . '.url')
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach
                            </div>

                            <button type="button" wire:click="addLink"
                                class="w-full px-4 py-2 text-gray-700 transition bg-gray-100 rounded-lg hover:bg-gray-200">
                                + Tambah Tautan
                            </button>
                        </div>

                        <!-- Footer (Locked - tidak bisa diubah) -->
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Footer
                                <span class="ml-2 px-2 py-0.5 text-xs bg-gray-200 text-gray-600 rounded-full">
                                    🔒 Terkunci
                                </span>
                            </label>
                            <div class="space-y-3">
                                <input type="text" value="{{ $footerText1 }}" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                                    placeholder="© 2025 Made with ❤ BPS Kota Tanjungpinang -">
                                <input type="text" value="{{ $footerText2 }}" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                                    placeholder="BPS Provinsi Kepulauan Riau">
                            </div>
                        </div>

                        <!-- Kustomisasi & Styling (sama seperti sebelumnya - terlalu panjang, saya skip bagian ini) -->
                        <!-- Kustomisasi & Styling Lanjutan -->
                        <div class="mb-6">
                            <label class="block mb-3 text-sm font-medium text-gray-700">
                                🎨 Kustomisasi & Styling Lanjutan
                            </label>

                            <!-- Navigasi Tab -->
                            <!-- 🎨 BAGIAN STYLING TABS - LENGKAP & FIXED -->
                            <!-- Ganti bagian ini di blade baru (mulai dari div x-data sampai penutup div styling) -->

                            <div x-data="{ activeTab: 'background' }" class="w-full">
                                <!-- Tombol Tab -->
                                <div class="flex flex-wrap mb-4 border-b border-gray-200">
                                    <button @click="activeTab = 'background'"
                                        :class="activeTab === 'background' ? 'border-blue-500 text-blue-600 bg-blue-50' :
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="px-4 py-2 text-sm font-medium transition-colors duration-200 border-b-2">
                                        🌈 Latar Belakang
                                    </button>

                                    <button @click="activeTab = 'typography'"
                                        :class="activeTab === 'typography' ? 'border-blue-500 text-blue-600 bg-blue-50' :
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="px-4 py-2 text-sm font-medium transition-colors duration-200 border-b-2">
                                        📝 Tulisan
                                    </button>

                                    <button @click="activeTab = 'buttons'"
                                        :class="activeTab === 'buttons' ? 'border-blue-500 text-blue-600 bg-blue-50' :
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="px-4 py-2 text-sm font-medium transition-colors duration-200 border-b-2">
                                        🔘 Tombol
                                    </button>

                                    <button @click="activeTab = 'branding'"
                                        :class="activeTab === 'branding' ? 'border-blue-500 text-blue-600 bg-blue-50' :
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="px-4 py-2 text-sm font-medium transition-colors duration-200 border-b-2">
                                        🏷️ Logo
                                    </button>


                                    <button @click="activeTab = 'footer'"
                                        :class="activeTab === 'footer' ? 'border-blue-500 text-blue-600 bg-blue-50' :
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="px-4 py-2 text-sm font-medium transition-colors duration-200 border-b-2">
                                        📄 Footer
                                    </button>

                                    <button @click="activeTab = 'qrcode'"
                                        :class="activeTab === 'qrcode' ? 'border-green-500 text-green-600 bg-green-50' :
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="px-4 py-2 text-sm font-medium transition-colors duration-200 border-b-2">
                                        📱 QR Code
                                    </button>

                                    <button @click="activeTab = 'advanced'"
                                        :class="activeTab === 'advanced' ? 'border-purple-500 text-purple-600 bg-purple-50' :
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="px-4 py-2 text-sm font-medium transition-colors duration-200 border-b-2">
                                        ⚙️ JSON
                                    </button>
                                </div>

                                <!-- Isi Tab -->
                                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">

                                    <!-- 🌈 Tab Latar Belakang -->
                                    <div x-show="activeTab === 'background'" x-transition>
                                        <h4 class="mb-3 text-base font-semibold text-gray-800">🌈 Latar Belakang &
                                            Wadah</h4>

                                        <!-- Gradien Latar Belakang -->
                                        <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                                            <!-- Warna Awal Gradien -->
                                            <div>
                                                <label class="block mb-2 text-sm font-medium text-gray-600">Warna Awal
                                                    Gradien</label>
                                                <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                    <div class="flex items-center gap-3 mb-3">
                                                        <input type="color" id="gradientStartColor" value="#002366"
                                                            class="border-2 border-gray-300 rounded-lg cursor-pointer w-14 h-14">
                                                        <div class="flex-1">
                                                            <label class="block mb-1 text-xs text-gray-600">
                                                                Transparansi: <span
                                                                    id="gradientStartOpacity">100%</span>
                                                            </label>
                                                            <input type="range" id="gradientStartRange"
                                                                min="0" max="100" value="100"
                                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                        </div>
                                                    </div>
                                                    <input type="text"
                                                        wire:model.blur="styles.background.gradientStart"
                                                        id="gradientStartOutput"
                                                        class="w-full px-3 py-2 font-mono text-sm border border-gray-300 rounded-lg"
                                                        placeholder="#002366">
                                                </div>
                                            </div>

                                            <!-- Warna Akhir Gradien -->
                                            <div>
                                                <label class="block mb-2 text-sm font-medium text-gray-600">Warna Akhir
                                                    Gradien</label>
                                                <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                    <div class="flex items-center gap-3 mb-3">
                                                        <input type="color" id="gradientEndColor" value="#1d4e5f"
                                                            class="border-2 border-gray-300 rounded-lg cursor-pointer w-14 h-14">
                                                        <div class="flex-1">
                                                            <label class="block mb-1 text-xs text-gray-600">
                                                                Transparansi: <span id="gradientEndOpacity">100%</span>
                                                            </label>
                                                            <input type="range" id="gradientEndRange"
                                                                min="0" max="100" value="100"
                                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                        </div>
                                                    </div>
                                                    <input type="text"
                                                        wire:model.blur="styles.background.gradientEnd"
                                                        id="gradientEndOutput"
                                                        class="w-full px-3 py-2 font-mono text-sm border border-gray-300 rounded-lg"
                                                        placeholder="#1D4E5F">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="block mb-1 text-sm font-medium text-gray-600">Arah Gradien
                                                <span class="text-xs text-gray-400">(derajat)</span></label>
                                            <div class="flex items-center gap-2">
                                                <input type="number" wire:model.blur="styles.background.direction"
                                                    class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    min="0" max="360" step="1" placeholder="135">
                                                <select wire:model.blur="styles.background.direction"
                                                    class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="0">0° ⬆️ (Bawah ke Atas)</option>
                                                    <option value="45">45° ↗️ (Kiri Bawah ke Kanan Atas)</option>
                                                    <option value="90">90° ➡️ (Kiri ke Kanan)</option>
                                                    <option value="135">135° ↘️ (Kiri Atas ke Kanan Bawah)</option>
                                                    <option value="180">180° ⬇️ (Atas ke Bawah)</option>
                                                    <option value="225">225° ↙️ (Kanan Atas ke Kiri Bawah)</option>
                                                    <option value="270">270° ⬅️ (Kanan ke Kiri)</option>
                                                    <option value="315">315° ↖️ (Kanan Bawah ke Kiri Atas)</option>
                                                    <option value="360">360° ⬆️ (Sama dengan 0°)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Gaya Wadah -->
                                        <div class="mb-6">
                                            <!-- Tipe Background Wadah -->
                                            <div class="mb-4">
                                                <label class="block mb-2 text-sm font-medium text-gray-600">Tipe Latar
                                                    Belakang Wadah</label>
                                                <select wire:model.blur="styles.container.backgroundType"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="solid">🎨 Warna Solid</option>
                                                    <option value="gradient">🌈 Gradien</option>
                                                </select>
                                            </div>

                                            <!-- Container Background Controls -->
                                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                                                <!-- Solid Background (show when solid) -->
                                                <div x-show="$wire.styles.container.backgroundType === 'solid'"
                                                    x-transition>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Solid</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-3 mb-3">
                                                            <input type="color" id="containerBgColor"
                                                                value="#ffffff"
                                                                class="border-2 border-gray-300 rounded-lg cursor-pointer w-14 h-14">
                                                            <div class="flex-1">
                                                                <label class="block mb-1 text-xs text-gray-600">
                                                                    Transparansi: <span
                                                                        id="containerBgOpacity">95%</span>
                                                                </label>
                                                                <input type="range" id="containerBgRange"
                                                                    min="0" max="100" value="95"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.container.backgroundColor"
                                                            id="containerBgOutput"
                                                            class="w-full px-3 py-2 font-mono text-sm border border-gray-300 rounded-lg"
                                                            placeholder="#FFFFFF">
                                                    </div>
                                                </div>

                                                <!-- Radius Sudut -->
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Radius
                                                        Sudut
                                                        <span class="text-xs text-gray-400">(px)</span></label>
                                                    <input type="number"
                                                        wire:model.blur="styles.container.borderRadius"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        min="0" max="50" step="1"
                                                        placeholder="24">
                                                </div>
                                            </div>

                                            <!-- Gradient Background Controls (show when gradient) -->
                                            <div x-show="$wire.styles.container.backgroundType === 'gradient'"
                                                x-transition>
                                                <h5 class="mb-3 text-sm font-medium text-gray-700">🌈 Pengaturan
                                                    Gradien Wadah</h5>
                                                <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-3">
                                                    <!-- Gradient Start -->
                                                    <div>
                                                        <label
                                                            class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                            Awal</label>
                                                        <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                            <div class="flex items-center gap-2 mb-2">
                                                                <input type="color" id="containerGradStartColor"
                                                                    value="#ffffff"
                                                                    class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                                <div class="flex-1">
                                                                    <label class="block mb-1 text-xs text-gray-600">
                                                                        Transparansi: <span
                                                                            id="containerGradStartOpacity">95%</span>
                                                                    </label>
                                                                    <input type="range" id="containerGradStartRange"
                                                                        min="0" max="100" value="95"
                                                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                                </div>
                                                            </div>
                                                            <input type="text"
                                                                wire:model.blur="styles.container.backgroundGradientStart"
                                                                id="containerGradStartOutput"
                                                                class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                                placeholder="#FFFFFF">
                                                        </div>
                                                    </div>

                                                    <!-- Gradient End -->
                                                    <div>
                                                        <label
                                                            class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                            Akhir</label>
                                                        <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                            <div class="flex items-center gap-2 mb-2">
                                                                <input type="color" id="containerGradEndColor"
                                                                    value="#f8fafc"
                                                                    class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                                <div class="flex-1">
                                                                    <label class="block mb-1 text-xs text-gray-600">
                                                                        Transparansi: <span
                                                                            id="containerGradEndOpacity">95%</span>
                                                                    </label>
                                                                    <input type="range" id="containerGradEndRange"
                                                                        min="0" max="100" value="95"
                                                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                                </div>
                                                            </div>
                                                            <input type="text"
                                                                wire:model.blur="styles.container.backgroundGradientEnd"
                                                                id="containerGradEndOutput"
                                                                class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                                placeholder="#F8FAFC">
                                                        </div>
                                                    </div>

                                                    <!-- Gradient Direction -->
                                                    <div>
                                                        <label
                                                            class="block mb-1 text-sm font-medium text-gray-600">Arah
                                                            Gradien</label>
                                                        <select
                                                            wire:model.blur="styles.container.backgroundGradientDirection"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                            <option value="0">0° ⬆️ (Bawah ke Atas)</option>
                                                            <option value="45">45° ↗️ (Kiri Bawah ke Kanan Atas)
                                                            </option>
                                                            <option value="90">90° ➡️ (Kiri ke Kanan)</option>
                                                            <option value="135">135° ↘️ (Kiri Atas ke Kanan Bawah)
                                                            </option>
                                                            <option value="180">180° ⬇️ (Atas ke Bawah)</option>
                                                            <option value="225">225° ↙️ (Kanan Atas ke Kiri Bawah)
                                                            </option>
                                                            <option value="270">270° ⬅️ (Kanan ke Kiri)</option>
                                                            <option value="315">315° ↖️ (Kanan Bawah ke Kiri Atas)
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Garis Aksen Atas -->
                                        <div class="mb-4">
                                            <h5 class="mb-3 text-sm font-medium text-gray-700">Garis Aksen Atas</h5>
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                                <!-- Warna Awal Garis Atas -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Awal</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="topGradientStartColor"
                                                                value="#ffd700"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="topGradientStartRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.container.topGradientStart"
                                                            id="topGradientStartOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFD700">
                                                    </div>
                                                </div>
                                                <!-- Warna Akhir Garis Atas -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Akhir</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="topGradientEndColor"
                                                                value="#002366"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="topGradientEndRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.container.topGradientEnd"
                                                            id="topGradientEndOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#002366">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Tinggi
                                                        <span class="text-xs text-gray-400">(px)</span></label>
                                                    <input type="number"
                                                        wire:model.blur="styles.container.topGradientHeight"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        min="1" max="20" step="1"
                                                        placeholder="4">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 📝 Tab Tulisan -->
                                    <div x-show="activeTab === 'typography'" x-transition>
                                        <h4 class="mb-3 text-base font-semibold text-gray-800">📝 Tulisan & Teks</h4>

                                        <!-- Gaya Judul -->
                                        <div class="mb-4">
                                            <h5 class="mb-2 font-medium text-gray-700">Gaya Judul</h5>
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                                <!-- Warna Judul -->
                                                <div>
                                                    <label
                                                        class="block mb-2 text-sm font-medium text-gray-600">Warna</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="titleColor" value="#002366"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="titleOpacityRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text" wire:model.blur="styles.title.color"
                                                            id="titleColorOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#002366">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Ukuran
                                                        Font
                                                        <span class="text-xs text-gray-400">(px)</span></label>
                                                    <input type="number" wire:model.blur="styles.title.fontSize"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        min="12" max="48" step="1"
                                                        placeholder="26">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block mb-1 text-sm font-medium text-gray-600">Ketebalan
                                                        Font</label>
                                                    <select wire:model.blur="styles.title.fontWeight"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="400">400 - Normal</option>
                                                        <option value="500">500 - Sedang</option>
                                                        <option value="600">600 - Tebal Sedang</option>
                                                        <option value="700">700 - Tebal</option>
                                                        <option value="800">800 - Sangat Tebal</option>
                                                        <option value="900">900 - Tebal Maksimal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Gaya Deskripsi -->
                                        <div>
                                            <h5 class="mb-2 font-medium text-gray-700">Gaya Deskripsi</h5>
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                <!-- Warna Deskripsi -->
                                                <div>
                                                    <label
                                                        class="block mb-2 text-sm font-medium text-gray-600">Warna</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="descColor" value="#666666"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="descOpacityRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.description.color"
                                                            id="descColorOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#666666">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Ukuran
                                                        Font
                                                        <span class="text-xs text-gray-400">(px)</span></label>
                                                    <input type="number"
                                                        wire:model.blur="styles.description.fontSize"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        min="10" max="24" step="1"
                                                        placeholder="15">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 🔘 Tab Tombol - LENGKAP dengan border hover -->
                                    <div x-show="activeTab === 'buttons'" x-transition>
                                        <h4 class="mb-3 text-base font-semibold text-gray-800">🔘 Gaya Tombol</h4>

                                        <!-- Status Normal Tombol -->
                                        <div class="mb-6">
                                            <h5 class="mb-2 font-medium text-gray-700">Status Normal</h5>
                                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                                                <!-- Latar Belakang Tombol -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Latar Belakang</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="buttonBgColor" value="#ffffff"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="buttonBgRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.button.backgroundColor"
                                                            id="buttonBgOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFFFFF">
                                                    </div>
                                                </div>
                                                <!-- Teks Tombol -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Teks</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="buttonTextColor"
                                                                value="#002366"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="buttonTextRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text" wire:model.blur="styles.button.color"
                                                            id="buttonTextOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#002366">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Border Tombol -->
                                            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                                <!-- Warna Border Tombol -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Border</label>
                                                    <div class="p-2 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <input type="color" id="buttonBorderColor"
                                                                value="#eaeaea"
                                                                class="w-8 h-8 border border-gray-300 rounded cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="buttonBorderRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-1 bg-gray-200 rounded appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.button.borderColor"
                                                            id="buttonBorderOutput"
                                                            class="w-full px-1 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#EAEAEA">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Lebar
                                                        <span class="text-xs text-gray-400">(px)</span></label>
                                                    <input type="number" wire:model.blur="styles.button.borderWidth"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        min="0" max="10" step="1"
                                                        placeholder="2">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block mb-1 text-sm font-medium text-gray-600">Gaya</label>
                                                    <select wire:model.blur="styles.button.borderStyle"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="solid">Garis Solid</option>
                                                        <option value="dashed">Garis Putus-putus</option>
                                                        <option value="dotted">Titik-titik</option>
                                                        <option value="none">Tanpa Border</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Radius
                                                        <span class="text-xs text-gray-400">(px)</span></label>
                                                    <input type="number" wire:model.blur="styles.button.borderRadius"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        min="0" max="50" step="1"
                                                        placeholder="12">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Hover Tombol - LENGKAP dengan border settings -->
                                        <div>
                                            <h5 class="mb-2 font-medium text-gray-700">Status Hover (Saat Diarahkan)
                                            </h5>
                                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                                                <!-- Latar Belakang Hover Awal -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Latar
                                                        Belakang Awal</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="hoverBgStartColor"
                                                                wire:model.live.debounce.300ms="styles.buttonHover.backgroundStart"
                                                                value="#ffd700"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <label class="block mb-1 text-xs text-gray-600">
                                                                    Transparansi: <span id="hoverBgStartOpacity">100%</span>
                                                                </label>
                                                                <input type="range" id="hoverBgStartRange"
                                                                    wire:model.live.debounce.300ms="styles.buttonHover.backgroundStartOpacity"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.buttonHover.backgroundStart"
                                                            id="hoverBgStartOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFD700">
                                                    </div>
                                                </div>
                                                <!-- Latar Belakang Hover Akhir -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Latar
                                                        Belakang Akhir</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="hoverBgEndColor"
                                                                wire:model.live.debounce.300ms="styles.buttonHover.backgroundEnd"
                                                                value="#ffffff"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <label class="block mb-1 text-xs text-gray-600">
                                                                    Transparansi: <span id="hoverBgEndOpacity">100%</span>
                                                                </label>
                                                                <input type="range" id="hoverBgEndRange"
                                                                    wire:model.live.debounce.300ms="styles.buttonHover.backgroundEndOpacity"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.buttonHover.backgroundEnd"
                                                            id="hoverBgEndOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFFFFF">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                                                <!-- Warna Teks Hover -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Teks Hover</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="hoverTextColor" value="#002366"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="hoverTextRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.buttonHover.color"
                                                            id="hoverTextOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#002366">
                                                    </div>
                                                </div>
                                                <!-- Warna Border Hover -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Border</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="hoverBorderColor"
                                                                value="#ffd700"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="hoverBorderRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.buttonHover.borderColor"
                                                            id="hoverBorderOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFD700">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ✅ TAMBAHAN: Border Width & Style untuk Hover -->
                                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Lebar
                                                        Border Hover
                                                        <span class="text-xs text-gray-400">(px)</span></label>
                                                    <input type="number"
                                                        wire:model.blur="styles.buttonHover.borderWidth"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        min="0" max="10" step="1"
                                                        placeholder="2">
                                                </div>
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Gaya
                                                        Border Hover</label>
                                                    <select wire:model.blur="styles.buttonHover.borderStyle"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="solid">Garis Solid</option>
                                                        <option value="dashed">Garis Putus-putus</option>
                                                        <option value="dotted">Titik-titik</option>
                                                        <option value="double">Garis Ganda</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                <!-- Warna Cahaya -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Cahaya</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="glowColor" value="#ffd700"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="glowRange" min="0"
                                                                    max="100" value="30"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.buttonHover.glowColor"
                                                            id="glowOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFD700">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Blur
                                                        Cahaya
                                                        <span class="text-xs text-gray-400">(px)</span></label>
                                                    <input type="number"
                                                        wire:model.blur="styles.buttonHover.glowBlur"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        min="0" max="100" step="5"
                                                        placeholder="30">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Continue dengan tab lainnya di artifact berikutnya... -->
                                    <!-- LANJUTAN TABS STYLING - Bagian 2 -->
                                    <!-- Paste setelah tab "buttons" di artifact sebelumnya -->

                                    <!-- 🏷️ Tab Logo & Brand - LENGKAP dengan border style -->
                                    <div x-show="activeTab === 'branding'" x-transition>
                                        <h4 class="mb-3 text-base font-semibold text-gray-800">🏷️ Logo & Branding</h4>

                                        <!-- Tipe Background Logo -->
                                        <div class="mb-4">
                                            <label class="block mb-2 text-sm font-medium text-gray-600">Tipe Latar
                                                Belakang Logo</label>
                                            <select wire:model.blur="styles.logo.backgroundType"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="solid">🎨 Warna Solid</option>
                                                <option value="gradient">🌈 Gradien</option>
                                            </select>
                                        </div>

                                        <!-- Background Controls -->
                                        <!-- Solid Background -->
                                        <div x-show="$wire.styles.logo.backgroundType === 'solid'" x-transition
                                            class="mb-6">
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                <!-- Warna Latar Belakang Logo Solid -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Latar Belakang</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="logoBgColor" value="#ffffff"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <label class="block mb-1 text-xs text-gray-600">
                                                                    Transparansi: <span id="logoBgOpacity">100%</span>
                                                                </label>
                                                                <input type="range" id="logoBgRange" min="0"
                                                                    max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.logo.backgroundColor"
                                                            id="logoBgOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFFFFF">
                                                    </div>
                                                </div>
                                                <!-- Warna Lingkaran Logo -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Lingkaran</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="logoRingColor" value="#ffd700"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <label class="block mb-1 text-xs text-gray-600">
                                                                    Transparansi: <span
                                                                        id="logoRingOpacity">100%</span>
                                                                </label>
                                                                <input type="range" id="logoRingRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.logo.borderColor"
                                                            id="logoRingOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFD700">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Gradient Background -->
                                        <div x-show="$wire.styles.logo.backgroundType === 'gradient'" x-transition
                                            class="mb-6">
                                            <h5 class="mb-3 text-sm font-medium text-gray-700">🌈 Pengaturan Gradien
                                                Logo</h5>
                                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-3">
                                                <!-- Gradient Start -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Awal</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="logoGradStartColor"
                                                                value="#ffffff"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <label class="block mb-1 text-xs text-gray-600">
                                                                    Transparansi: <span
                                                                        id="logoGradStartOpacity">100%</span>
                                                                </label>
                                                                <input type="range" id="logoGradStartRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.logo.backgroundGradientStart"
                                                            id="logoGradStartOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFFFFF">
                                                    </div>
                                                </div>

                                                <!-- Gradient End -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Akhir</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="logoGradEndColor"
                                                                value="#f8fafc"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <label class="block mb-1 text-xs text-gray-600">
                                                                    Transparansi: <span
                                                                        id="logoGradEndOpacity">100%</span>
                                                                </label>
                                                                <input type="range" id="logoGradEndRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.logo.backgroundGradientEnd"
                                                            id="logoGradEndOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#F8FAFC">
                                                    </div>
                                                </div>

                                                <!-- Gradient Direction -->
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Arah
                                                        Gradien</label>
                                                    <select wire:model.blur="styles.logo.backgroundGradientDirection"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="0">0° ⬆️ (Bawah ke Atas)</option>
                                                        <option value="45">45° ↗️ (Kiri Bawah ke Kanan Atas)
                                                        </option>
                                                        <option value="90">90° ➡️ (Kiri ke Kanan)</option>
                                                        <option value="135">135° ↘️ (Kiri Atas ke Kanan Bawah)
                                                        </option>
                                                        <option value="180">180° ⬇️ (Atas ke Bawah)</option>
                                                        <option value="225">225° ↙️ (Kanan Atas ke Kiri Bawah)
                                                        </option>
                                                        <option value="270">270° ⬅️ (Kanan ke Kiri)</option>
                                                        <option value="315">315° ↖️ (Kanan Bawah ke Kiri Atas)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Border Logo (shared for gradient) -->
                                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-1">
                                                <!-- Warna Lingkaran Logo (untuk gradient) -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Lingkaran</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="logoRingColorGrad"
                                                                value="#ffd700"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <label class="block mb-1 text-xs text-gray-600">
                                                                    Transparansi: <span
                                                                        id="logoRingGradOpacity">100%</span>
                                                                </label>
                                                                <input type="range" id="logoRingRangeGrad"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.logo.borderColor"
                                                            id="logoRingOutputGrad"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#FFD700">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ✅ LENGKAP: Pengaturan Lebar, Gaya, dan Radius -->
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                            <!-- Lebar Lingkaran -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-600">Lebar
                                                    Lingkaran
                                                    <span class="text-xs text-gray-400">(px)</span></label>
                                                <input type="number" wire:model.blur="styles.logo.borderWidth"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    min="0" max="20" step="1" placeholder="3">
                                            </div>

                                            <!-- ✅ TAMBAHAN: Gaya Lingkaran Dropdown -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-600">Gaya
                                                    Lingkaran</label>
                                                <select wire:model.blur="styles.logo.borderStyle"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="solid">Garis Solid</option>
                                                    <option value="dashed">Garis Putus-putus</option>
                                                    <option value="dotted">Titik-titik</option>
                                                    <option value="double">Garis Ganda</option>
                                                    <option value="groove">Alur</option>
                                                    <option value="ridge">Punggungan</option>
                                                    <option value="inset">Inset</option>
                                                    <option value="outset">Outset</option>
                                                </select>
                                            </div>

                                            <!-- Radius Sudut Logo -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-600">Radius
                                                    Sudut
                                                    <span class="text-xs text-gray-400">(px)</span></label>
                                                <input type="number" wire:model.blur="styles.logo.borderRadius"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    min="0" max="100" step="1"
                                                    placeholder="50 (lingkaran penuh)">
                                            </div>
                                        </div>
                                    </div>


                                    <!-- 📄 Tab Footer - LENGKAP -->
                                    <div x-show="activeTab === 'footer'" x-transition>
                                        <h4 class="mb-3 text-base font-semibold text-gray-800">📄 Styling Footer</h4>

                                        <!-- Gaya Teks Footer -->
                                        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
                                            <!-- Warna Teks Footer -->
                                            <div>
                                                <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                    Teks</label>
                                                <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <input type="color" id="footerTextColor" value="#999999"
                                                            class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                        <div class="flex-1">
                                                            <input type="range" id="footerTextRange" min="0"
                                                                max="100" value="100"
                                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                        </div>
                                                    </div>
                                                    <input type="text" wire:model.blur="styles.footer.color"
                                                        id="footerTextOutput"
                                                        class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                        placeholder="#999999">
                                                </div>
                                            </div>

                                            <!-- Ukuran Font Footer -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-600">Ukuran
                                                    Font
                                                    <span class="text-xs text-gray-400">(px)</span></label>
                                                <input type="number" wire:model.blur="styles.footer.fontSize"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    min="8" max="20" step="1" placeholder="12">
                                            </div>
                                        </div>

                                        <!-- Garis Atas Footer -->
                                        <div class="mb-6">
                                            <h5 class="mb-3 text-sm font-medium text-gray-700">Garis Pemisah</h5>
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                <!-- Warna Garis Footer -->
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-600">Warna
                                                        Garis</label>
                                                    <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <input type="color" id="footerBorderColor"
                                                                value="#eeeeee"
                                                                class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                            <div class="flex-1">
                                                                <input type="range" id="footerBorderRange"
                                                                    min="0" max="100" value="100"
                                                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                            wire:model.blur="styles.footer.borderColor"
                                                            id="footerBorderOutput"
                                                            class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                            placeholder="#EEEEEE">
                                                    </div>
                                                </div>

                                                <!-- Spacing Controls -->
                                                <div class="space-y-3">
                                                    <div>
                                                        <label
                                                            class="block mb-1 text-sm font-medium text-gray-600">Jarak
                                                            dari Konten
                                                            <span class="text-xs text-gray-400">(px)</span></label>
                                                        <input type="number"
                                                            wire:model.blur="styles.footer.marginTop"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                            min="10" max="100" step="5"
                                                            placeholder="30">
                                                    </div>

                                                    <div>
                                                        <label
                                                            class="block mb-1 text-sm font-medium text-gray-600">Jarak
                                                            dari Garis
                                                            <span class="text-xs text-gray-400">(px)</span></label>
                                                        <input type="number"
                                                            wire:model.blur="styles.footer.paddingTop"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                            min="5" max="50" step="5"
                                                            placeholder="20">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 📱 Tab QR Code - BARU & LENGKAP -->
                                    <!-- 📱 Tab QR Code - SIMPLE TANPA COLOR PICKER -->
                                    <div x-show="activeTab === 'qrcode'" x-transition>

                                        <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                                            <strong>ℹ️ Info:</strong> QR Code akan otomatis mengarah ke URL halaman ini.
                                            User bisa scan untuk buka halaman di HP.
                                        </div>

                                        <!-- Enable QR Code -->
                                        <div class="mb-4">
                                            <label class="flex items-center space-x-3">
                                                <input type="checkbox" wire:model.live="styles.qrcode.enabled"
                                                    class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                                <span class="text-sm font-medium text-gray-700">Aktifkan QR Code di
                                                    Halaman</span>
                                            </label>
                                        </div>

                                        <div x-show="$wire.styles.qrcode.enabled" x-transition class="space-y-4">

                                            <!-- Info Default Warna -->
                                            <div
                                                class="p-3 text-sm text-gray-700 border border-gray-200 rounded-lg bg-gray-50">
                                                <strong>🎨 Warna Default:</strong> Background QR putih, Border Container
                                                putih, QR Code hitam (standard).
                                            </div>

                                            <!-- Position & Size -->
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Posisi
                                                        QR Code</label>
                                                    <select wire:model.live="styles.qrcode.position"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                                        <option value="bottom-right">↘️ Kanan Bawah</option>
                                                        <option value="bottom-left">↙️ Kiri Bawah</option>
                                                        <option value="top-right">↗️ Kanan Atas</option>
                                                        <option value="top-left">↖️ Kiri Atas</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block mb-1 text-sm font-medium text-gray-600">Ukuran
                                                        <span class="text-xs text-gray-400">(px)</span></label>
                                                    <input type="number" wire:model.live="styles.qrcode.size"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                                        min="50" max="300" step="10"
                                                        placeholder="100">
                                                </div>
                                            </div>

                                            <!-- Border Radius, Padding & Colors -->
                                            <div class="space-y-4">
                                                <!-- Baris 1: Border Radius & Padding -->
                                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label
                                                            class="block mb-1 text-sm font-medium text-gray-600">Border
                                                            Radius
                                                            <span class="text-xs text-gray-400">(px)</span></label>
                                                        <input type="number"
                                                            wire:model.live="styles.qrcode.borderRadius"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                                            min="0" max="50" step="1"
                                                            placeholder="12">
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block mb-1 text-sm font-medium text-gray-600">Padding
                                                            <span class="text-xs text-gray-400">(px)</span></label>
                                                        <input type="number"
                                                            wire:model.live="styles.qrcode.padding"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                                            min="0" max="50" step="5"
                                                            placeholder="10">
                                                    </div>
                                                </div>

                                                <!-- ✅ BARIS 2: 3 KONTROL WARNA TERPISAH UNTUK QR CODE -->
                                                <div class="space-y-4">
                                                    <!-- Background QR Color - LIVESTREAM BINDING -->
                                                    <div>
                                                        <label class="block mb-2 text-sm font-medium text-gray-600">🎨
                                                            Background QR</label>
                                                        <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                            <div class="flex items-center gap-2 mb-2">
                                                                <input type="color"
                                                                    wire:model.live="qrBackgroundColorPicker"
                                                                    value="#ffffff"
                                                                    class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                                <div class="flex-1">
                                                                    <label class="block mb-1 text-xs text-gray-600">
                                                                        Warna HEX: <span
                                                                            class="font-mono">{{ $qrBackgroundColorPicker ?? '#FFFFFF' }}</span>
                                                                    </label>
                                                                    <label class="block mb-1 text-xs text-gray-600">
                                                                        Transparansi:
                                                                        <span>{{ $qrBackgroundOpacity ?? 100 }}%</span>
                                                                    </label>
                                                                    <input type="range"
                                                                        wire:model.live="qrBackgroundOpacity"
                                                                        min="0" max="100"
                                                                        value="100"
                                                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                                </div>
                                                            </div>
                                                            <input type="text"
                                                                wire:model.live="styles.qrcode.backgroundColor"
                                                                class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                                placeholder="#FFFFFF">
                                                        </div>
                                                    </div>

                                                    <!-- Border Container Color - LIVESTREAM BINDING -->
                                                    <div>
                                                        <label
                                                            class="block mb-2 text-sm font-medium text-gray-600">🏷️
                                                            Border Container</label>
                                                        <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                            <div class="flex items-center gap-2 mb-2">
                                                                <input type="color"
                                                                    wire:model.live="qrBorderColorPicker"
                                                                    value="#ffffff"
                                                                    class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                                <div class="flex-1">
                                                                    <label class="block mb-1 text-xs text-gray-600">
                                                                        Warna HEX: <span
                                                                            class="font-mono">{{ $qrBorderColorPicker ?? '#FFFFFF' }}</span>
                                                                    </label>
                                                                    <label class="block mb-1 text-xs text-gray-600">
                                                                        Transparansi:
                                                                        <span>{{ $qrBorderOpacity ?? 100 }}%</span>
                                                                    </label>
                                                                    <input type="range"
                                                                        wire:model.live="qrBorderOpacity"
                                                                        min="0" max="100"
                                                                        value="100"
                                                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                                </div>
                                                            </div>
                                                            <input type="text"
                                                                wire:model.live="styles.qrcode.borderColor"
                                                                class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                                placeholder="#FFFFFF">
                                                        </div>
                                                    </div>

                                                    <!-- Pattern QR Color - LIVESTREAM BINDING -->
                                                    <div>
                                                        <label class="block mb-2 text-sm font-medium text-gray-600">🔲
                                                            Pattern QR Code</label>
                                                        <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                            <div class="flex items-center gap-2 mb-2">
                                                                <input type="color"
                                                                    wire:model.live="qrDarkColorPicker"
                                                                    value="#000000"
                                                                    class="w-12 h-12 border-2 border-gray-300 rounded-lg cursor-pointer">
                                                                <div class="flex-1">
                                                                    <label class="block mb-1 text-xs text-gray-600">
                                                                        Warna HEX: <span
                                                                            class="font-mono">{{ $qrDarkColorPicker ?? '#000000' }}</span>
                                                                    </label>
                                                                    <label class="block mb-1 text-xs text-gray-600">
                                                                        Transparansi:
                                                                        <span>{{ $qrDarkOpacity ?? 100 }}%</span>
                                                                    </label>
                                                                    <input type="range"
                                                                        wire:model.live="qrDarkOpacity"
                                                                        min="0" max="100"
                                                                        value="100"
                                                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                                                                </div>
                                                            </div>
                                                            <input type="text"
                                                                wire:model.live="styles.qrcode.darkColor"
                                                                class="w-full px-2 py-1 font-mono text-xs border border-gray-300 rounded"
                                                                placeholder="#000000">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tooltip Text -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-600">Tooltip
                                                    Text</label>
                                                <input type="text" wire:model.live="styles.qrcode.tooltipText"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                                    placeholder="Scan QR untuk buka...">
                                            </div>
                                            <!-- Info: Shadow & Hover sudah default aktif -->
                                            <div
                                                class="p-3 text-xs text-gray-600 border border-blue-200 rounded-lg bg-blue-50">
                                                <strong>✨ Default:</strong> Shadow effect dan hover zoom sudah aktif. QR
                                                Code hidden di mobile. <br>
                                                <strong>🎨 Full Controls:</strong> Gunakan warna HEX + transparansi
                                                (0-100%) untuk semua kontrol QR. <br>
                                                <strong>✅ YA BISA!:</strong> Transparansi sudah bisa diatur untuk semua
                                                3 warna QR!
                                            </div>
                                        </div>
                                    </div>


                                    <!-- ⚙️ Tab Advanced -->
                                    <div x-show="activeTab === 'advanced'" x-transition>
                                        <h4 class="mb-3 text-base font-semibold text-purple-800">⚙️ Mode JSON Lanjutan
                                        </h4>

                                        <div class="p-3 mb-3 text-xs text-purple-700 bg-purple-100 rounded-lg">
                                            <strong>🔥 Mode Override JSON:</strong><br>
                                            Edit JSON untuk kontrol penuh. Klik "Terapkan JSON" untuk menerapkan ke
                                            semua tab styling!
                                        </div>

                                        <textarea wire:model.live="stylesJson" rows="16"
                                            class="w-full px-3 py-2 font-mono text-xs border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                            placeholder='Paste JSON styling dari AI atau edit manual'></textarea>

                                        <!-- Tombol Terapkan dengan Toggle System -->
                                        @php
                                            $applyButtonConfig = [
                                                'enable_gradient' => true,
                                                'enable_shadow' => true,
                                                'enable_hover_effects' => true,
                                                'enable_animations' => true,
                                                'enable_border_radius' => true,
                                                'enable_transitions' => true,
                                            ];
                                        @endphp
                                        <div class="flex gap-2 mt-3">
                                            <button type="button" wire:click="applyJsonStyles"
                                                class="flex-1 px-4 py-2 text-white
                                                    @if ($applyButtonConfig['enable_gradient']) bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 @else bg-purple-500 hover:bg-purple-600 @endif
                                                    @if ($applyButtonConfig['enable_shadow']) shadow-md @endif
                                                    @if ($applyButtonConfig['enable_border_radius']) rounded-lg @else rounded-none @endif
                                                    @if ($applyButtonConfig['enable_transitions']) transition @endif
                                                    @if ($applyButtonConfig['enable_hover_effects']) transform hover:scale-105 @endif
                                                    @if ($applyButtonConfig['enable_animations']) button-animation-enabled @endif
                                                    apply-styles-button">
                                                <svg class="inline w-4 h-4 mr-1" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                                    </path>
                                                </svg>
                                                Terapkan JSON
                                            </button>

                                            @php
                                                $copyButtonConfig = [
                                                    'enable_gradient' => false,
                                                    'enable_shadow' => true,
                                                    'enable_hover_effects' => true,
                                                    'enable_animations' => true,
                                                    'enable_border_radius' => true,
                                                    'enable_transitions' => true,
                                                ];
                                            @endphp
                                            <button type="button" onclick="copyJsonToClipboard()"
                                                class="flex-1 px-4 py-2 text-purple-700
                                                    @if ($copyButtonConfig['enable_gradient']) bg-gradient-to-r from-purple-100 to-pink-100 hover:from-purple-200 hover:to-pink-200 @else bg-purple-100 hover:bg-purple-200 @endif
                                                    @if ($copyButtonConfig['enable_shadow']) shadow-md @endif
                                                    @if ($copyButtonConfig['enable_border_radius']) rounded-lg @else rounded-none @endif
                                                    @if ($copyButtonConfig['enable_transitions']) transition @endif
                                                    @if ($copyButtonConfig['enable_hover_effects']) transform hover:scale-105 @endif
                                                    @if ($copyButtonConfig['enable_animations']) button-animation-enabled @endif
                                                    copy-button">
                                                <svg class="inline w-4 h-4 mr-1" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                🤖 Copy Prompt + JSON ke AI
                                            </button>
                                        </div>

                                        <div class="p-3 mt-3 text-xs text-purple-600 bg-purple-100 rounded-lg">
                                            <strong>📄 Cara kerja:</strong><br>
                                            1. Edit JSON di atas atau paste JSON baru dari AI<br>
                                            2. Klik "Terapkan JSON" untuk update semua pengatur warna<br>
                                            3. Preview akan otomatis update dengan styling baru<br><br>
                                            <strong>🤖 Copy Prompt + JSON ke AI:</strong><br>
                                            Klik tombol → Langsung paste ke ChatGPT/Claude → Ganti [TEMA] dengan
                                            keinginan → Copy hasil JSON → Paste di sini → Terapkan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Anda bisa copy paste dari document asli bagian styling tabs -->

                        <!-- Tombol Aksi dengan Toggle System -->
                        @php
                            // Button Styling Configuration - Toggle individual features
                            $buttonConfig = [
                                'enable_gradient' => true,
                                'enable_shadow' => true,
                                'enable_hover_effects' => true,
                                'enable_animations' => true,
                                'enable_loading_spin' => true,
                                'enable_disabled_state' => true,
                                'enable_border_radius' => true,
                                'enable_transitions' => true,
                            ];
                        @endphp

                        <div class="flex gap-2 mb-4">
                            <button type="button" wire:click="saveToDatabase" wire:loading.attr="disabled"
                                wire:target="saveToDatabase"
                                class="flex-1 px-4 py-2 text-white
                                    @if ($buttonConfig['enable_gradient']) bg-gradient-to-r from-blue-600 via-green-600 to-orange-600 hover:from-blue-700 hover:via-green-700 hover:to-orange-700 @else bg-blue-500 hover:bg-blue-600 @endif
                                    @if ($buttonConfig['enable_shadow']) shadow-lg @endif
                                    @if ($buttonConfig['enable_border_radius']) rounded-lg @else rounded-none @endif
                                    @if ($buttonConfig['enable_transitions']) transition @endif
                                    @if ($buttonConfig['enable_hover_effects']) transform hover:scale-105 @endif
                                    @if ($buttonConfig['enable_disabled_state']) disabled:opacity-50 @endif
                                    @if ($buttonConfig['enable_animations']) button-animation-enabled @endif
                                    save-button">
                                @if ($buttonConfig['enable_loading_spin'])
                                    <svg wire:loading wire:target="saveToDatabase"
                                        class="inline w-4 h-4 mr-2 text-white animate-spin" viewBox="0 0 100 101"
                                        fill="none">
                                        <path
                                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                            fill="#E5E7EB" />
                                        <path
                                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                            fill="currentColor" />
                                    </svg>
                                @endif
                                <svg wire:loading.remove wire:target="saveToDatabase" class="inline w-5 h-5 mr-2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <span wire:loading.remove wire:target="saveToDatabase">💾 Simpan & Publikasikan</span>
                                <span wire:loading wire:target="saveToDatabase">Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bagian Live Preview dengan Toggle Mobile/Desktop + Fullscreen -->
                <div class="sticky top-0 z-10" style="height: fit-content;" x-data="{
                    viewMode: 'mobile',
                    isFullscreen: false,
                    toggleFullscreen() {
                        this.isFullscreen = !this.isFullscreen;
                        const sidebar = document.getElementById('logo-sidebar');
                        const mainContent = document.querySelector('.sm\\:ml-64');
                        if (this.isFullscreen) {
                            if (sidebar) sidebar.style.display = 'none';
                            if (mainContent) mainContent.style.marginLeft = '0';
                            document.body.style.overflow = 'hidden';
                        } else {
                            if (sidebar) sidebar.style.display = '';
                            if (mainContent) mainContent.style.marginLeft = '';
                            document.body.style.overflow = '';
                        }
                    }
                }">
                    <div class="bg-white shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <!-- Header dengan Toggle Switch -->
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">
                                    👁️ Live Preview
                                    <span class="ml-2 text-sm font-normal text-gray-500">Auto-update</span>
                                </h3>

                                <!-- Toggle Button Mobile/Desktop + Fullscreen -->
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-2 p-1 bg-gray-100 rounded-lg">
                                        <button @click="viewMode = 'mobile'; if(isFullscreen) toggleFullscreen();"
                                            :class="viewMode === 'mobile' ? 'bg-white shadow-sm text-blue-600' :
                                                'text-gray-600'"
                                            class="flex items-center gap-1 px-3 py-2 text-sm font-medium transition-all rounded-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            📱 Mobile
                                        </button>
                                        <button @click="viewMode = 'desktop'"
                                            :class="viewMode === 'desktop' ? 'bg-white shadow-sm text-blue-600' :
                                                'text-gray-600'"
                                            class="flex items-center gap-1 px-3 py-2 text-sm font-medium transition-all rounded-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            💻 Desktop
                                        </button>
                                    </div>

                                    <!-- Fullscreen Button (hanya muncul di mode desktop) -->
                                    <button x-show="viewMode === 'desktop'" @click="toggleFullscreen()"
                                        :class="isFullscreen ? 'bg-red-500 text-white hover:bg-red-600' :
                                            'bg-purple-500 text-white hover:bg-purple-600'"
                                        class="flex items-center gap-1 px-3 py-2 text-sm font-medium transition-all rounded-lg shadow-md"
                                        x-transition>
                                        <svg x-show="!isFullscreen" class="w-4 h-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                            </path>
                                        </svg>
                                        <svg x-show="isFullscreen" class="w-4 h-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span x-text="isFullscreen ? 'Tutup' : 'Fullscreen'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Mobile Preview (Default) -->
                            <div x-show="viewMode === 'mobile'" x-transition class="flex justify-center mb-4">
                                <div class="relative transition-all duration-300"
                                    style="width: 375px; height: 667px;">
                                    <div class="absolute inset-0 bg-gray-900 rounded-[40px] shadow-2xl"></div>
                                    <div
                                        class="absolute w-40 h-6 transform -translate-x-1/2 bg-gray-800 rounded-full top-6 left-1/2">
                                    </div>
                                    <div
                                        class="absolute top-12 bottom-12 left-4 right-4 bg-white rounded-[20px] overflow-hidden">
                                        <iframe id="previewFrame" srcdoc="{{ $this->previewHtml }}"
                                            class="w-full h-full"
                                            sandbox="allow-scripts allow-same-origin"
                                            style="border: none;"></iframe>
                                    </div>
                                </div>
                            </div>

                            <!-- Desktop Preview (Normal Mode) -->
                            <div x-show="viewMode === 'desktop' && !isFullscreen" x-transition
                                class="mb-4 overflow-hidden transition-all duration-300">
                                <div class="relative mx-auto" style="max-width: 100%; height: 650px;">
                                    <!-- Browser Chrome -->
                                    <div
                                        class="absolute inset-x-0 top-0 h-10 bg-gray-200 border-b border-gray-300 rounded-t-lg">
                                        <div class="flex items-center h-full px-4 space-x-2">
                                            <div class="flex space-x-2">
                                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                            </div>
                                            <div
                                                class="flex-1 px-4 py-1.5 ml-3 text-xs text-gray-700 bg-white rounded-md shadow-sm">
                                                <span class="text-gray-400">🔒</span>
                                                {{ !empty($this->slug) ? route('tautan.show', $this->slug) : 'preview.local' }}
                                            </div>
                                            <div class="flex gap-1">
                                                <div class="w-5 h-5 text-gray-500">
                                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Browser Content -->
                                    <div class="absolute inset-0 overflow-auto bg-white rounded-lg shadow-2xl top-10">
                                        <iframe srcdoc="{{ $this->previewHtml }}" class="w-full h-full"
                                            sandbox="allow-scripts allow-same-origin"
                                            style="border: none;"></iframe>
                                    </div>
                                </div>
                            </div>

                            <div class="text-sm text-center text-gray-500" x-show="!isFullscreen">
                                <p>💡 Preview otomatis update saat Anda mengetik</p>
                                <p class="mt-1">
                                    <span x-show="viewMode === 'mobile'">📱 Preview dalam tampilan mobile</span>
                                    <span x-show="viewMode === 'desktop'">💻 Preview dalam tampilan desktop</span>
                                </p>
                                @if (!empty($this->slug))
                                    <p class="mt-2 text-xs">
                                        <strong>URL Internal:</strong><br>
                                        <code
                                            class="bg-gray-100 px-1 py-0.5 rounded text-xs">{{ route('tautan.show', $this->slug) }}</code>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Fullscreen Modal Overlay -->
                    <div x-show="isFullscreen" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="fixed inset-0 z-[9999] bg-black bg-opacity-90"
                        style="display: none;">

                        <!-- Close Button -->
                        <button @click="toggleFullscreen()"
                            class="fixed z-50 flex items-center gap-2 px-4 py-2 text-white transition bg-red-600 rounded-lg shadow-lg top-4 right-4 hover:bg-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Tutup Fullscreen (ESC)
                        </button>

                        <!-- Fullscreen Preview Container -->
                        <div class="relative w-full h-full p-4">
                            <div class="relative w-full h-full mx-auto">
                                <!-- Browser Chrome Fullscreen -->
                                <div
                                    class="absolute inset-x-0 top-0 h-12 bg-gray-800 border-b border-gray-700 rounded-t-lg">
                                    <div class="flex items-center h-full px-6 space-x-3">
                                        <div class="flex space-x-2">
                                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        </div>
                                        <div
                                            class="flex-1 px-4 py-2 ml-4 text-sm text-white bg-gray-700 rounded-md shadow-sm">
                                            <span class="text-gray-300">🔒</span>
                                            {{ !empty($this->slug) ? route('tautan.show', $this->slug) : 'preview.local' }}
                                        </div>
                                        <div class="px-3 py-1 text-xs text-gray-300 bg-gray-700 rounded">
                                            Fullscreen Preview
                                        </div>
                                    </div>
                                </div>
                                <!-- Browser Content Fullscreen -->
                                <div class="absolute inset-0 overflow-auto bg-white rounded-lg shadow-2xl top-12">
                                    <iframe srcdoc="{{ $this->previewHtml }}" class="w-full h-full"
                                        sandbox="allow-scripts allow-same-origin allow-images"
                                        style="border: none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Script untuk ESC key -->
                <script>
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            const previewComponent = document.querySelector('[x-data*="isFullscreen"]');
                            if (previewComponent) {
                                const alpineData = Alpine.$data(previewComponent);
                                if (alpineData && alpineData.isFullscreen) {
                                    alpineData.toggleFullscreen();
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div>
    </div>

    <!-- Icon Picker Modal -->
    @if($showIconPicker)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/30 backdrop-blur-sm p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[85vh] flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold">Pilih Icon</h3>
                    <button wire:click="closeIconPicker" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Icon Grid -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="grid grid-cols-10 gap-1" id="iconGrid">
                        @php
                            $iconCategories = [
                                'Populer' => ['fa-star', 'fa-heart', 'fa-home', 'fa-user', 'fa-envelope', 'fa-phone', 'fa-globe', 'fa-link', 'fa-download', 'fa-check'],
                                'Media' => ['fa-image', 'fa-camera', 'fa-video', 'fa-music', 'fa-microphone', 'fa-film', 'fa-headphones', 'fa-photo-film', 'fa-play', 'fa-pause'],
                                'File' => ['fa-file', 'fa-file-pdf', 'fa-file-word', 'fa-file-excel', 'fa-file-powerpoint', 'fa-file-zipper', 'fa-file-code', 'fa-file-audio', 'fa-file-video', 'fa-file-image'],
                                'Sosial' => ['fa-facebook', 'fa-twitter', 'fa-instagram', 'fa-youtube', 'fa-linkedin', 'fa-tiktok', 'fa-whatsapp', 'fa-telegram', 'fa-discord', 'fa-github'],
                                'E-commerce' => ['fa-cart-shopping', 'fa-bag-shopping', 'fa-wallet', 'fa-credit-card', 'fa-money-bill', 'fa-receipt', 'fa-truck', 'fa-box', 'fa-tag', 'fa-percent'],
                                'Teknologi' => ['fa-laptop', 'fa-mobile', 'fa-tablet', 'fa-desktop', 'fa-wifi', 'fa-bluetooth', 'fa-battery-full', 'fa-charging-station', 'fa-qrcode', 'fa-barcode'],
                                'Kantor' => ['fa-building', 'fa-city', 'fa-store', 'fa-warehouse', 'fa-industry', 'fa-landmark', 'fa-calendar', 'fa-clock', 'fa-briefcase', 'fa-id-card'],
                                'Data' => ['fa-chart-bar', 'fa-chart-line', 'fa-chart-pie', 'fa-database', 'fa-server', 'fa-cloud', 'fa-table', 'fa-spreadsheet', 'fa-analytics', 'fa-project-diagram'],
                                'Transport' => ['fa-car', 'fa-truck', 'fa-bus', 'fa-train', 'fa-plane', 'fa-ship', 'fa-motorcycle', 'fa-bicycle', 'fa-taxi', 'fa-rocket'],
                                'Cuaca' => ['fa-sun', 'fa-moon', 'fa-cloud', 'fa-cloud-sun', 'fa-cloud-rain', 'fa-bolt', 'fa-snowflake', 'fa-temperature-half', 'fa-wind', 'fa-water'],
                            ];
                        @endphp

                        @foreach($iconCategories as $category => $icons)
                            <div class="contents">
                                <div class="col-span-10 text-xs font-semibold text-gray-500 uppercase tracking-wide py-2 mt-2">{{ $category }}</div>
                                @foreach($icons as $icon)
                                    <button wire:click="setIcon('fa-solid {{ $icon }}')"
                                        class="flex items-center justify-center w-full aspect-square p-2 rounded hover:bg-purple-100 transition-colors group">
                                        <i class="fa-solid {{ $icon }} text-gray-600 group-hover:text-purple-600"></i>
                                    </button>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between p-4 border-t">
                    <button wire:click="removeIcon"
                        class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <i class="fas fa-times mr-1"></i> Hapus Icon
                    </button>
                    <button wire:click="closeIconPicker"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Fungsi untuk deteksi dan mengatur tema otomatis berdasarkan preferensi sistem
        function detectAndSetSystemTheme() {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            // Jika tidak ada tema yang tersimpan, gunakan preferensi sistem
            if (!savedTheme) {
                const theme = systemPrefersDark ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            } else {
                // Gunakan tema yang tersimpan
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
        }

        // Inisialisasi tema sebelum DOM loaded
        detectAndSetSystemTheme();

        // Listener untuk perubahan preferensi sistem
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            const savedTheme = localStorage.getItem('theme');
            // Hanya update otomatis jika user tidak pernah mengatur tema manual
            if (!savedTheme || savedTheme === 'system') {
                const newTheme = e.matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            }
        });

        // ===== VARIABEL GLOBAL =====
        let initializedPickers = new Set();
        let updateTimeouts = new Map();
        let isProcessingUpload = false;

        // ===== MAIN INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan tema sudah terdeteksi dengan benar
            detectAndSetSystemTheme();

            console.log('🚀 Initializing Taut Pinang form...');

            // Inisialisasi komponen utama
            initializeRadioButtons();
            initializeOptimizedColorPickers();
            setupFileUploadHandler();
            setupSlugFormatter();
            initializeSortableLinks();

            console.log('✅ All components initialized');

            // Add event listeners for hover color pickers to update preview in real-time
            setTimeout(() => {
                const hoverColorPickers = [
                    'hoverBgStartColor',
                    'hoverBgEndColor',
                    'hoverTextColor',
                    'hoverBorderColor',
                    'hoverGlowColor'
                ];

                hoverColorPickers.forEach(pickerId => {
                    const picker = document.getElementById(pickerId);
                    if (picker) {
                        picker.addEventListener('input', function() {
                            console.log(`🎨 Hover color changed: ${pickerId} = ${this.value}`);
                            // Update hover effects in preview immediately
                            const iframe = document.getElementById('previewFrame');
                            if (iframe) {
                                setTimeout(() => {
                                    addHoverEffectsToPreview(iframe);
                                }, 100);
                            }
                        });
                    }
                });

                // Also listen to range slider changes
                const hoverRangeSliders = [
                    'hoverBorderRange',
                    'hoverGlowRange'
                ];

                hoverRangeSliders.forEach(sliderId => {
                    const slider = document.getElementById(sliderId);
                    if (slider) {
                        slider.addEventListener('input', function() {
                            console.log(`🎨 Hover range changed: ${sliderId} = ${this.value}`);
                            // Update hover effects in preview immediately
                            const iframe = document.getElementById('previewFrame');
                            if (iframe) {
                                setTimeout(() => {
                                    addHoverEffectsToPreview(iframe);
                                }, 100);
                            }
                        });
                    }
                });
            }, 1000);

            // ===== LIVEWIRE HOOKS (SATU TEMPAT UNTUK SEMUA) =====

            // Hook utama untuk update setelah Livewire response
            Livewire.hook('commit', ({ component, succeed }) => {
                succeed(() => {
                    queueMicrotask(() => {
                        if (component.name !== 'buat-tautan') return;

                        try {
                            // Update preview iframe
                            updatePreviewIframe();

                            // Update radio button state berdasarkan Livewire state terbaru
                            updateRadioButtonsFromLivewire(component);

                            // Update range sliders dengan delay untuk memastikan DOM ready
                            setTimeout(() => {
                                updateAllRangeSlidersFromLivewire();
                                // Refresh QR colors from Livewire state
                                refreshQRColorsFromLivewire();

                                // CRITICAL: Re-apply hover effects after Livewire update
                                const iframe = document.getElementById('previewFrame');
                                if (iframe) {
                                    addHoverEffectsToPreview(iframe);
                                }
                            }, 300); // Increased delay to ensure iframe is fully loaded

                            // Reset processing flag
                            isProcessingUpload = false;

                            console.log('📡 Livewire message processed successfully');
                        } catch (error) {
                            console.warn('⚠️ Error in Livewire message.processed hook:', error);
                        }
                    });
                });
            });

            // Hook untuk validation errors
            Livewire.hook('commit', ({ commit, fail }) => {
                fail(() => {
                        if (commit.calls && commit.calls.some(update => update.method ===
                                'saveToDatabase')) {
                            showToast('❌ Mohon periksa form, ada data yang belum valid!', 'error');
                        }
                });
            });

            // Hook untuk prevent update during upload
            Livewire.hook('morph.updating', ({ el, skip }) => {
                if (isProcessingUpload && (el.id === 'previewFrame' || el.id === 'miniPreview')) {
                    console.log('🚫 Preventing update during upload for element:', el.id);
                    skip();
                }
            });

            // ===== LIVEWIRE EVENT LISTENERS =====

            // Handler update JSON
            Livewire.on('stylesUpdated', () => {
                showToast('✨ JSON berhasil update UI styling fields!', 'success');
                setTimeout(() => {
                    updateAllColorPickersFromStyles();
                    // Update hover effects in preview
                    const iframe = document.getElementById('previewFrame');
                    if (iframe) {
                        addHoverEffectsToPreview(iframe);
                    }
                }, 200);
            });

            // Toast untuk Save Database
            Livewire.on('databaseSaved', () => {
                showToast('🎉 Tautan berhasil disimpan ke database!', 'success');
            });

            Livewire.on('databaseError', ({ message }) => {
                showToast('❌ Gagal simpan: ' + message, 'error');
            });

            // Toast untuk Generate HTML
            Livewire.on('htmlGenerated', () => {
                showToast('📄 HTML berhasil dibuat! Siap untuk GitHub Pages.', 'success');
            });

            Livewire.on('htmlError', ({ message }) => {
                showToast('❌ Gagal generate HTML: ' + message, 'error');
            });

            // ===== BROWSER EVENTS =====

            // Handle logo uploaded browser event
            window.addEventListener('logoUploaded', handleLogoUploadedEvent);

            // Initialize QR Color Sync System
            // QR Color controls now use Livewire model binding - no JavaScript needed

            console.log('🎯 All event listeners registered');
        });

        // ===== RADIO BUTTON INITIALIZATION & MANAGEMENT =====

        function initializeRadioButtons() {
            console.log('🔘 Initializing FREE TOGGLE radio buttons...');

            setTimeout(() => {
                const urlRadio = document.getElementById('logoUrl');
                const uploadRadio = document.getElementById('logoUpload');

                if (!urlRadio || !uploadRadio) {
                    console.warn('⚠️ Radio buttons not found in DOM');
                    return;
                }

                // PERBAIKAN: Set state dari Livewire TANPA BLOCKING
                let useUploadedLogo = false;
                try {
                    if (typeof @this !== 'undefined' && @this.get) {
                        useUploadedLogo = @this.get('useUploadedLogo') || false;
                    }
                } catch (error) {
                    console.log('⚡ Livewire belum ready, menggunakan default state');
                    useUploadedLogo = false;
                }

                // PERBAIKAN: Set state TANPA DISABLE logic
                urlRadio.checked = !useUploadedLogo;
                uploadRadio.checked = useUploadedLogo;
                urlRadio.disabled = false; // SELALU AKTIF
                uploadRadio.disabled = false; // SELALU AKTIF

                // PERBAIKAN: Add free toggle event listeners
                urlRadio.addEventListener('click', function(e) {
                    console.log('🔗 URL radio clicked - BEBAS TOGGLE');
                    this.checked = true;
                    uploadRadio.checked = false;

                    try {
                        @this.set('useUploadedLogo', false);
                        console.log('✅ Livewire updated to URL mode');
                    } catch (error) {
                        console.warn('⚠️ Could not update Livewire, but radio works');
                    }
                });

                uploadRadio.addEventListener('click', function(e) {
                    console.log('📁 Upload radio clicked - BEBAS TOGGLE');
                    this.checked = true;
                    urlRadio.checked = false;

                    try {
                        @this.set('useUploadedLogo', true);
                        console.log('✅ Livewire updated to Upload mode');
                    } catch (error) {
                        console.warn('⚠️ Could not update Livewire, but radio works');
                    }
                });

                console.log('✅ FREE TOGGLE radio buttons initialized - useUploadedLogo:', useUploadedLogo);
            }, 150);
        }

        function updateRadioButtonsFromLivewire(component) {
            try {
                const useUploadedLogo = component.$wire.get('useUploadedLogo');
                const hasUploadedImage = component.$wire.get('hasUploadedImage');
                const currentLogoSource = (useUploadedLogo ? component.$wire.get('logoPreviewUrl') : component.$wire.get('logoUrl'));

                // PERBAIKAN: Update radio buttons TANPA BLOCKING
                updateRadioButtonState(useUploadedLogo, hasUploadedImage);

                // Update mini preview
                updateMiniPreview(currentLogoSource);

                console.log('🔄 Radio buttons updated - ALWAYS FREE TO TOGGLE');
                console.log('useUploadedLogo:', useUploadedLogo, 'hasUploadedImage:', hasUploadedImage);
            } catch (error) {
                console.warn('⚠️ Error updating radio buttons from Livewire:', error);
            }
        }

        function updateRadioButtonState(useUploadedLogo, hasUploadedImage) {
            const urlRadio = document.getElementById('logoUrl');
            const uploadRadio = document.getElementById('logoUpload');

            if (!urlRadio || !uploadRadio) return;

            // PERBAIKAN: Hanya update checked state, JANGAN DISABLE apapun
            urlRadio.checked = !useUploadedLogo;
            uploadRadio.checked = useUploadedLogo;

            // PERBAIKAN: HILANGKAN SEMUA DISABLE LOGIC
            urlRadio.disabled = false; // SELALU AKTIF
            uploadRadio.disabled = false; // SELALU AKTIF

            // PERBAIKAN: Reset semua visual disabled state
            urlRadio.className = urlRadio.className.replace(/opacity-\d+|cursor-[\w-]+/g, '').trim();
            uploadRadio.className = uploadRadio.className.replace(/opacity-\d+|cursor-[\w-]+/g, '').trim();

            // PERBAIKAN: Labels selalu aktif
            const urlLabel = urlRadio.nextElementSibling;
            const uploadLabel = uploadRadio.nextElementSibling;

            if (urlLabel) {
                urlLabel.className = urlLabel.className.replace(/text-gray-\d+/g, '').trim() + ' text-gray-900';
            }
            if (uploadLabel) {
                uploadLabel.className = uploadLabel.className.replace(/text-gray-\d+/g, '').trim() + ' text-gray-900';
            }

            console.log('🆓 Radio buttons state - URL disabled: false, Upload disabled: false (ALWAYS FREE)');
        }


        // ===== PREVIEW & UI UPDATE FUNCTIONS =====

        function updateMiniPreview(newSrc) {
            const miniPreview = document.getElementById('miniPreview');
            if (!miniPreview || !newSrc) return;

            if (newSrc !== miniPreview.currentSrc) {
                miniPreview.style.transition = 'opacity 0.3s ease';
                miniPreview.style.opacity = '0.7';

                miniPreview.src = newSrc;
                miniPreview.currentSrc = newSrc;

                miniPreview.onload = function() {
                    this.style.opacity = '1';
                };

                // Fallback jika gagal load dalam 2 detik
                setTimeout(() => {
                    if (miniPreview.style.opacity !== '1') {
                        miniPreview.style.opacity = '1';
                    }
                }, 2000);
            }
        }

        function updatePreviewIframe() {
            const iframe = document.getElementById('previewFrame');
            if (iframe) {
                const newSrcdoc = iframe.getAttribute('srcdoc');
                if (newSrcdoc && iframe.srcdoc !== newSrcdoc) {
                    iframe.srcdoc = newSrcdoc;

                    // Tambahkan hover effects setelah iframe load dengan multiple attempts
                    let attempts = 0;
                    const maxAttempts = 10;

                    const tryAddHoverEffects = () => {
                        attempts++;
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        const buttons = iframeDoc ? iframeDoc.querySelectorAll('.link-button') : [];

                        if (buttons.length > 0) {
                            console.log(`✅ Found ${buttons.length} buttons in preview, adding hover effects...`);
                            addHoverEffectsToPreview(iframe);
                        } else if (attempts < maxAttempts) {
                            console.log(`⏳ Attempt ${attempts}: No buttons found yet, retrying...`);
                            setTimeout(tryAddHoverEffects, 200);
                        } else {
                            console.warn('❌ Could not find buttons in preview after multiple attempts');
                        }
                    };

                    setTimeout(tryAddHoverEffects, 100);
                }
            }
        }

        function addHoverEffectsToPreview(iframe) {
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                if (!iframeDoc) {
                    console.warn('⚠️ Cannot access iframe document');
                    return;
                }

                // 🎯 PERBAIKAN: CSS SUDAH MENGHANDLE HOVER, JANGAN GUNAKAN JAVASCRIPT!
                // CSS sudah mencakup semua hover effects (background gradient, color, border, glow)
                // Jangan tambah JavaScript event listeners karena akan override CSS hover

                // Get current hover settings from Livewire component (untuk debug saja) - FIXED
                if (typeof @this === 'undefined') {
                    console.warn('Livewire component not available for hover effects debug');
                    return;
                }

                const styles = @this.get('styles') || {};
                const buttonHover = styles.buttonHover || {};

                console.log('✅ CSS Hover is active with settings:', {
                    backgroundStart: buttonHover.backgroundStart || '#FFD700',
                    backgroundEnd: buttonHover.backgroundEnd || '#FFFFFF',
                    textColor: buttonHover.color || '#002366',
                    borderColor: buttonHover.borderColor || '#FFD700',
                    borderWidth: buttonHover.borderWidth || 2,
                    borderStyle: buttonHover.borderStyle || 'solid',
                    glowColor: buttonHover.glowColor || '#FFD700',
                    glowBlur: buttonHover.glowBlur || 30
                });

                // Hapus JavaScript hover handlers yang mungkin ada
                const linkButtons = iframeDoc.querySelectorAll('.link-button');
                console.log(`🔍 Found ${linkButtons.length} link buttons, removing JavaScript hover handlers`);

                linkButtons.forEach((button, index) => {
                    // Hapus JavaScript hover handlers agar CSS hover bisa bekerja
                    if (button._hoverEnterHandler) {
                        button.removeEventListener('mouseenter', button._hoverEnterHandler);
                        delete button._hoverEnterHandler;
                    }
                    if (button._hoverLeaveHandler) {
                        button.removeEventListener('mouseleave', button._hoverLeaveHandler);
                        delete button._hoverLeaveHandler;
                    }

                    // Reset inline styles yang mungkin mengganggu CSS hover
                    // JANGAN hapus background dan color default karena ini diperlukan
                    // CSS akan menghandle perubahan saat hover

                    button.classList.add('css-hover-enabled');
                });

                console.log(`✅ CSS Hover effects enabled for ${linkButtons.length} buttons (JavaScript disabled)`);
            } catch (error) {
                console.warn('⚠️ Error setting up CSS hover:', error);
            }
        }

        // ===== SETUP FUNCTIONS =====

        function setupFileUploadHandler() {
            const fileInput = document.getElementById('logoUploadInput');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    if (e.target.files && e.target.files.length > 0) {
                        isProcessingUpload = true;

                        // Visual feedback saat mulai upload
                        const uploadRadio = document.getElementById('logoUpload');
                        if (uploadRadio && uploadRadio.parentElement) {
                            const label = uploadRadio.parentElement;
                            label.style.background = '#dbeafe';
                            label.style.borderRadius = '6px';
                            label.style.padding = '4px 8px';
                            label.style.transition = 'all 0.3s ease';

                            // Reset setelah 5 detik
                            setTimeout(() => {
                                label.style.background = '';
                                label.style.borderRadius = '';
                                label.style.padding = '';
                            }, 5000);
                        }

                        console.log('📁 File selected for upload:', e.target.files[0].name);
                    }
                });

                console.log('✅ File upload handler set up');
            }
        }

        function setupSlugFormatter() {
            setTimeout(() => {
                const slugInput = document.querySelector('input[wire\\:model\\.debounce\\.300ms="slug"]');
                if (slugInput) {
                    slugInput.addEventListener('input', function(e) {
                        let value = e.target.value.toLowerCase();
                        value = value.replace(/[^a-z0-9\-_]/g, '');
                        if (value !== e.target.value) {
                            e.target.value = value;
                            e.target.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    });

                    console.log('✅ Slug formatter set up');
                }
            }, 500);
        }

        function handleLogoUploadedEvent(event) {
            console.log('📸 Logo uploaded event received:', event.detail);

            // PERBAIKAN: JANGAN AUTO-SWITCH TOGGLE
            // Biarkan user memilih mode sendiri

            // Update preview jika ada URL baru
            if (event.detail.logoPreviewUrl) {
                updateMiniPreview(event.detail.logoPreviewUrl);
            }

            // Visual success feedback pada container upload
            const uploadArea = document.querySelector('.upload-area, [wire\\:loading][wire\\:target="logoUpload"]');
            if (uploadArea) {
                uploadArea.classList.add('state-success');
                setTimeout(() => {
                    uploadArea.classList.remove('state-success');
                }, 3000);
            }

            isProcessingUpload = false;
            console.log('✅ Upload completed - Toggle tetap pada pilihan user');
        }

        // ===== COLOR PICKER SYSTEM =====

        function updateAllRangeSlidersFromLivewire() {
            const rangeMappings = [
                // Background
                ['gradientStartOutput', 'gradientStartRange', 'gradientStartOpacity'],
                ['gradientEndOutput', 'gradientEndRange', 'gradientEndOpacity'],
                ['containerBgOutput', 'containerBgRange', 'containerBgOpacity'],
                ['containerGradStartOutput', 'containerGradStartRange', 'containerGradStartOpacity'],
                ['containerGradEndOutput', 'containerGradEndRange', 'containerGradEndOpacity'],
                ['topGradientStartOutput', 'topGradientStartRange'],
                ['topGradientEndOutput', 'topGradientEndRange'],

                // Typography
                ['titleColorOutput', 'titleOpacityRange'],
                ['descColorOutput', 'descOpacityRange'],

                // Buttons
                ['buttonBgOutput', 'buttonBgRange'],
                ['buttonTextOutput', 'buttonTextRange'],
                ['buttonBorderOutput', 'buttonBorderRange'],
                ['hoverBgStartOutput', 'hoverBgStartRange'],
                ['hoverBgEndOutput', 'hoverBgEndRange'],
                ['hoverTextOutput', 'hoverTextRange'],
                ['hoverBorderOutput', 'hoverBorderRange'],
                ['glowOutput', 'glowRange'],

                // Logo
                ['logoBgOutput', 'logoBgRange', 'logoBgOpacity'],
                ['logoGradStartOutput', 'logoGradStartRange', 'logoGradStartOpacity'],
                ['logoGradEndOutput', 'logoGradEndRange', 'logoGradEndOpacity'],
                ['logoRingOutput', 'logoRingRange', 'logoRingOpacity'],
                ['logoRingOutputGrad', 'logoRingRangeGrad', 'logoRingGradOpacity'],

                // Footer
                ['footerTextOutput', 'footerTextRange'],
                ['footerBorderOutput', 'footerBorderRange'],

                // ✅ QR CODE - Simple HEX controls (no range sliders needed)
                // QR controls now use simple HEX without range sliders
            ];

            let updatedCount = 0;

            rangeMappings.forEach(([outputId, rangeId, opacityId]) => {
                const outputField = document.getElementById(outputId);
                const rangeField = document.getElementById(rangeId);

                if (outputField && outputField.value && rangeField) {
                    let percentValue = 100; // Default 100% untuk HEX
                    let hexColor = null;

                    // Cek apakah format RGBA
                    const rgbaMatch = outputField.value.match(/rgba\([\d\s,]+,\s*([\d.]+)\)/);
                    if (rgbaMatch) {
                        // Format RGBA - parse opacity
                        const alphaValue = parseFloat(rgbaMatch[1]);
                        percentValue = Math.round(alphaValue * 100);

                        // Update color picker dari RGBA
                        const colorPickerId = outputId.replace('Output', 'Color');
                        const colorPicker = document.getElementById(colorPickerId);
                        if (colorPicker) {
                            const rgbMatch = outputField.value.match(/rgba\((\d+),\s*(\d+),\s*(\d+)/);
                            if (rgbMatch) {
                                hexColor = rgbToHex(
                                    parseInt(rgbMatch[1]),
                                    parseInt(rgbMatch[2]),
                                    parseInt(rgbMatch[3])
                                );
                                colorPicker.value = hexColor;
                            }
                        }
                    } else {
                        // Format HEX - set opacity ke 100%
                        hexColor = outputField.value;

                        // Update color picker dari HEX
                        const colorPickerId = outputId.replace('Output', 'Color');
                        const colorPicker = document.getElementById(colorPickerId);
                        if (colorPicker) {
                            colorPicker.value = hexColor;
                        }
                    }

                    // Update range slider
                    rangeField.value = percentValue;

                    // Update opacity display
                    if (opacityId) {
                        const opacitySpan = document.getElementById(opacityId);
                        if (opacitySpan) {
                            opacitySpan.textContent = percentValue + '%';
                        }
                    }

                    updatedCount++;
                }
            });

            if (updatedCount > 0) {
                console.log('🔄 Updated', updatedCount, 'range sliders from Livewire');
            }
        }


        function initializeOptimizedColorPickers() {
            // Format: [colorInputId, rangeInputId, opacityDisplayId, outputId, livewireTarget]
            const colorPickers = [
                // ========== TAB: BACKGROUND ==========
                ['gradientStartColor', 'gradientStartRange', 'gradientStartOpacity', 'gradientStartOutput',
                    'styles.background.gradientStart'
                ],
                ['gradientEndColor', 'gradientEndRange', 'gradientEndOpacity', 'gradientEndOutput',
                    'styles.background.gradientEnd'
                ],
                ['containerBgColor', 'containerBgRange', 'containerBgOpacity', 'containerBgOutput',
                    'styles.container.backgroundColor'
                ],
                ['containerGradStartColor', 'containerGradStartRange', 'containerGradStartOpacity',
                    'containerGradStartOutput', 'styles.container.backgroundGradientStart'
                ],
                ['containerGradEndColor', 'containerGradEndRange', 'containerGradEndOpacity', 'containerGradEndOutput',
                    'styles.container.backgroundGradientEnd'
                ],
                ['topGradientStartColor', 'topGradientStartRange', '', 'topGradientStartOutput',
                    'styles.container.topGradientStart'
                ],
                ['topGradientEndColor', 'topGradientEndRange', '', 'topGradientEndOutput',
                    'styles.container.topGradientEnd'
                ],

                // ========== TAB: TYPOGRAPHY ==========
                ['titleColor', 'titleOpacityRange', '', 'titleColorOutput', 'styles.title.color'],
                ['descColor', 'descOpacityRange', '', 'descColorOutput', 'styles.description.color'],

                // ========== TAB: BUTTONS ==========
                ['buttonBgColor', 'buttonBgRange', '', 'buttonBgOutput', 'styles.button.backgroundColor'],
                ['buttonTextColor', 'buttonTextRange', '', 'buttonTextOutput', 'styles.button.color'],
                ['buttonBorderColor', 'buttonBorderRange', '', 'buttonBorderOutput', 'styles.button.borderColor'],
                ['hoverBgStartColor', 'hoverBgStartRange', '', 'hoverBgStartOutput',
                    'styles.buttonHover.backgroundStart'
                ],
                ['hoverBgEndColor', 'hoverBgEndRange', '', 'hoverBgEndOutput', 'styles.buttonHover.backgroundEnd'],
                ['hoverTextColor', 'hoverTextRange', '', 'hoverTextOutput', 'styles.buttonHover.color'],
                ['hoverBorderColor', 'hoverBorderRange', '', 'hoverBorderOutput', 'styles.buttonHover.borderColor'],
                ['glowColor', 'glowRange', '', 'glowOutput', 'styles.buttonHover.glowColor'],

                // ========== TAB: BRANDING (LOGO) ==========
                ['logoBgColor', 'logoBgRange', 'logoBgOpacity', 'logoBgOutput', 'styles.logo.backgroundColor'],
                ['logoGradStartColor', 'logoGradStartRange', 'logoGradStartOpacity', 'logoGradStartOutput',
                    'styles.logo.backgroundGradientStart'
                ],
                ['logoGradEndColor', 'logoGradEndRange', 'logoGradEndOpacity', 'logoGradEndOutput',
                    'styles.logo.backgroundGradientEnd'
                ],
                ['logoRingColor', 'logoRingRange', 'logoRingOpacity', 'logoRingOutput', 'styles.logo.borderColor'],
                ['logoRingColorGrad', 'logoRingRangeGrad', 'logoRingGradOpacity', 'logoRingOutputGrad',
                    'styles.logo.borderColor'
                ],

                // ========== TAB: FOOTER ==========
                ['footerTextColor', 'footerTextRange', '', 'footerTextOutput', 'styles.footer.color'],
                ['footerBorderColor', 'footerBorderRange', '', 'footerBorderOutput', 'styles.footer.borderColor'],

                // ========== TAB: QR CODE - ✅ 3 KONTROL WARNA TERPISAH ==========
                // QR controls now use Livewire model binding - no JavaScript needed
                // Dikelola secara terpisah untuk avoid bentrok dengan setupOptimizedColorPicker
            ];

            let initCount = 0;

            // Loop through each color picker configuration
            colorPickers.forEach(([colorId, rangeId, opacityId, outputId, target]) => {
                // Only initialize if not already initialized (prevent double binding)
                if (!initializedPickers.has(colorId)) {
                    setupOptimizedColorPicker(colorId, rangeId, opacityId, outputId, target);
                    initializedPickers.add(colorId);
                    initCount++;
                }
            });

            // Set initial values for QR Code colors from Livewire state (3 color controls)
            setTimeout(() => {
                try {
                    // Get current values from Livewire
                    const currentBackgroundColor = @this.get('styles.qrcode.backgroundColor');
                    const currentBorderColor = @this.get('styles.qrcode.borderColor');
                    const currentDarkColor = @this.get('styles.qrcode.darkColor');

                    console.log('🎨 3 QR Colors from Livewire:', {
                        backgroundColor: currentBackgroundColor,
                        borderColor: currentBorderColor,
                        darkColor: currentDarkColor
                    });

                    // QR Code colors now use Livewire model binding - no JavaScript needed

                    console.log('✅ QR Color controls use Livewire model binding - no JavaScript needed');
                } catch (error) {
                    console.log('⚡ QR Color init fallback - using defaults:', error);

                    // Fallback to defaults for 3 color controls with opacity
                    const qrBackgroundOutput = document.getElementById('qrBackgroundOutput');
                    const qrBackgroundColor = document.getElementById('qrBackgroundColor');
                    const qrBackgroundHex = document.getElementById('qrBackgroundHex');
                    const qrBackgroundRange = document.getElementById('qrBackgroundRange');
                    const qrBackgroundOpacity = document.getElementById('qrBackgroundOpacity');

                    if (qrBackgroundOutput && qrBackgroundColor && qrBackgroundHex) {
                        if (!qrBackgroundOutput.value || qrBackgroundOutput.value === '') {
                            qrBackgroundOutput.value = '#ffffff';
                            qrBackgroundColor.value = '#ffffff';
                            qrBackgroundHex.textContent = '#FFFFFF';
                            if (qrBackgroundRange) qrBackgroundRange.value = 100;
                            if (qrBackgroundOpacity) qrBackgroundOpacity.textContent = '100%';
                            console.log('🎯 Set QR Background default values (with opacity)');
                        }
                    }

                    const qrBorderOutput = document.getElementById('qrBorderOutput');
                    const qrBorderColor = document.getElementById('qrBorderColor');
                    const qrBorderHex = document.getElementById('qrBorderHex');
                    const qrBorderRange = document.getElementById('qrBorderRange');
                    const qrBorderOpacity = document.getElementById('qrBorderOpacity');

                    if (qrBorderOutput && qrBorderColor && qrBorderHex) {
                        if (!qrBorderOutput.value || qrBorderOutput.value === '') {
                            qrBorderOutput.value = '#ffffff';
                            qrBorderColor.value = '#ffffff';
                            qrBorderHex.textContent = '#FFFFFF';
                            if (qrBorderRange) qrBorderRange.value = 100;
                            if (qrBorderOpacity) qrBorderOpacity.textContent = '100%';
                            console.log('🎯 Set QR Border default values (with opacity)');
                        }
                    }

                    const qrDarkOutput = document.getElementById('qrDarkOutput');
                    const qrDarkColor = document.getElementById('qrDarkColor');
                    const qrDarkHex = document.getElementById('qrDarkHex');
                    const qrDarkRange = document.getElementById('qrDarkRange');
                    const qrDarkOpacity = document.getElementById('qrDarkOpacity');

                    if (qrDarkOutput && qrDarkColor && qrDarkHex) {
                        if (!qrDarkOutput.value || qrDarkOutput.value === '') {
                            qrDarkOutput.value = '#000000';
                            qrDarkColor.value = '#000000';
                            qrDarkHex.textContent = '#000000';
                            if (qrDarkRange) qrDarkRange.value = 100;
                            if (qrDarkOpacity) qrDarkOpacity.textContent = '100%';
                            console.log('🎯 Set QR Dark default values (with opacity)');
                        }
                    }
                }
            }, 800);

            console.log('🎨 Initialized', initCount, 'color pickers (✅ QR: 3 color controls added!)');
        }

        function updateAllColorPickersFromStyles() {
            const colorPickerMappings = [
                // Background
                ['gradientStartColor', 'gradientStartRange', 'gradientStartOutput'],
                ['gradientEndColor', 'gradientEndRange', 'gradientEndOutput'],
                ['containerBgColor', 'containerBgRange', 'containerBgOutput'],
                ['containerGradStartColor', 'containerGradStartRange', 'containerGradStartOutput'],
                ['containerGradEndColor', 'containerGradEndRange', 'containerGradEndOutput'],
                ['topGradientStartColor', 'topGradientStartRange', 'topGradientStartOutput'],
                ['topGradientEndColor', 'topGradientEndRange', 'topGradientEndOutput'],

                // Typography
                ['titleColor', 'titleOpacityRange', 'titleColorOutput'],
                ['descColor', 'descOpacityRange', 'descColorOutput'],

                // Buttons
                ['buttonBgColor', 'buttonBgRange', 'buttonBgOutput'],
                ['buttonTextColor', 'buttonTextRange', 'buttonTextOutput'],
                ['buttonBorderColor', 'buttonBorderRange', 'buttonBorderOutput'],
                ['hoverBgStartColor', 'hoverBgStartRange', 'hoverBgStartOutput'],
                ['hoverBgEndColor', 'hoverBgEndRange', 'hoverBgEndOutput'],
                ['hoverTextColor', 'hoverTextRange', 'hoverTextOutput'],
                ['hoverBorderColor', 'hoverBorderRange', 'hoverBorderOutput'],
                ['glowColor', 'glowRange', 'glowOutput'],

                // Logo
                ['logoBgColor', 'logoBgRange', 'logoBgOutput'],
                ['logoGradStartColor', 'logoGradStartRange', 'logoGradStartOutput'],
                ['logoGradEndColor', 'logoGradEndRange', 'logoGradEndOutput'],
                ['logoRingColor', 'logoRingRange', 'logoRingOutput'],
                ['logoRingColorGrad', 'logoRingRangeGrad', 'logoRingOutputGrad'],

                // Footer
                ['footerTextColor', 'footerTextRange', 'footerTextOutput'],
                ['footerBorderColor', 'footerBorderRange', 'footerBorderOutput'],

                // QR Code colors now use Livewire model binding - no JavaScript needed
            ];

            let syncedCount = 0;

            colorPickerMappings.forEach(([colorId, rangeId, outputId]) => {
                const colorInput = document.getElementById(colorId);
                const rangeInput = rangeId ? document.getElementById(rangeId) : null;
                const textInput = document.getElementById(outputId);

                // Handle color controls with and without range slider
                if (colorInput && textInput && textInput.value) {
                    let hexColor = null;
                    let opacityPercent = 100; // Default 100% untuk HEX

                    // Cek apakah format RGBA
                    const rgba = parseRGBAValue(textInput.value);
                    if (rgba) {
                        // Format RGBA - konversi ke HEX
                        hexColor = rgbToHex(rgba.r, rgba.g, rgba.b);
                        opacityPercent = Math.round(rgba.a * 100);
                    } else {
                        // Format HEX - gunakan langsung
                        hexColor = textInput.value.startsWith('#') ? textInput.value : '#' + textInput.value;
                    }

                    // Update color picker
                    colorInput.value = hexColor;

                    // Update slider opacity (only if range exists)
                    if (rangeInput) {
                        rangeInput.value = opacityPercent;

                        // Update opacity display if exists
                        const possibleOpacitySelectors = [
                            `#${colorId.replace('Color', 'Opacity')}`,
                            `#${rangeId.replace('Range', 'Opacity')}`,
                            `span[id*="${colorId.replace('Color', 'Opacity')}"]`
                        ];

                        possibleOpacitySelectors.forEach(selector => {
                            const opacityDisplay = document.querySelector(selector);
                            if (opacityDisplay) {
                                opacityDisplay.textContent = opacityPercent + '%';
                            }
                        });
                    } else {
                        // Update HEX display for simple controls
                        const hexDisplayId = colorId.replace('Color', 'Hex');
                        const hexDisplay = document.getElementById(hexDisplayId);
                        if (hexDisplay) {
                            hexDisplay.textContent = hexColor.toUpperCase();
                        }
                    }

                    syncedCount++;
                }
            });

            if (syncedCount > 0) {
                console.log('✅ Synced', syncedCount, 'color pickers from JSON');
            }
        }

        function refreshQRColorsFromLivewire() {
            // Function to refresh QR colors from Livewire state and sync with color pickers
            console.log('🔄 refreshQRColorsFromLivewire() called');

            try {
                // Get QR color values from Livewire state using @this (Alpine/Livewire method)
                if (typeof @this === 'undefined') {
                    console.warn('Livewire component not available for QR color refresh');
                    return;
                }

                // Get QR color values from Livewire state
                const qrBgColor = @this.get('qrBackgroundColorPicker');
                const qrBorderColor = @this.get('qrBorderColorPicker');
                const qrDarkColor = @this.get('qrDarkColorPicker');
                const qrBgOpacity = @this.get('qrBackgroundOpacity');
                const qrBorderOpacity = @this.get('qrBorderOpacity');
                const qrDarkOpacity = @this.get('qrDarkOpacity');

                console.log('🎨 Refreshing QR Colors from Livewire:');
                console.log('   BG Color:', qrBgColor, 'Opacity:', qrBgOpacity + '%');
                console.log('   Border Color:', qrBorderColor, 'Opacity:', qrBorderOpacity + '%');
                console.log('   Dark Color:', qrDarkColor, 'Opacity:', qrDarkOpacity + '%');

                // Update QR color picker inputs
                const bgPicker = document.getElementById('qrBackgroundColorPicker');
                const borderPicker = document.getElementById('qrBorderColorPicker');
                const darkPicker = document.getElementById('qrDarkColorPicker');

                if (bgPicker && qrBgColor) {
                    bgPicker.value = qrBgColor;
                    console.log('✅ Updated QR Background Color picker');
                }
                if (borderPicker && qrBorderColor) {
                    borderPicker.value = qrBorderColor;
                    console.log('✅ Updated QR Border Color picker');
                }
                if (darkPicker && qrDarkColor) {
                    darkPicker.value = qrDarkColor;
                    console.log('✅ Updated QR Dark Color picker');
                }

                // Update opacity sliders
                const bgOpacitySlider = document.getElementById('qrBackgroundOpacity');
                const borderOpacitySlider = document.getElementById('qrBorderOpacity');
                const darkOpacitySlider = document.getElementById('qrDarkOpacity');

                if (bgOpacitySlider && qrBgOpacity !== undefined) {
                    bgOpacitySlider.value = qrBgOpacity;
                    const bgOutput = document.getElementById('qrBackgroundOutput');
                    if (bgOutput) {
                        bgOutput.textContent = qrBgOpacity + '%';
                    }
                    console.log('✅ Updated QR Background opacity');
                }

                if (borderOpacitySlider && qrBorderOpacity !== undefined) {
                    borderOpacitySlider.value = qrBorderOpacity;
                    const borderOutput = document.getElementById('qrBorderOutput');
                    if (borderOutput) {
                        borderOutput.textContent = qrBorderOpacity + '%';
                    }
                    console.log('✅ Updated QR Border opacity');
                }

                if (darkOpacitySlider && qrDarkOpacity !== undefined) {
                    darkOpacitySlider.value = qrDarkOpacity;
                    const darkOutput = document.getElementById('qrDarkOutput');
                    if (darkOutput) {
                        darkOutput.textContent = qrDarkOpacity + '%';
                    }
                    console.log('✅ Updated QR Dark opacity');
                }

                console.log('✅ QR Colors refreshed successfully from Livewire state');

            } catch (error) {
                console.warn('Error refreshing QR colors from Livewire:', error);
            }
        }
        // Parse string RGBA ke object
        function parseRGBAValue(rgbaStr) {
            const match = rgbaStr.match(/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/);
            if (match) {
                return {
                    r: parseInt(match[1]),
                    g: parseInt(match[2]),
                    b: parseInt(match[3]),
                    a: parseFloat(match[4])
                };
            }
            return null;
        }

        // Konversi RGB ke Hex
        function rgbToHex(r, g, b) {
            const toHex = (n) => {
                const hex = n.toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            };
            return '#' + toHex(r) + toHex(g) + toHex(b);
        }

        // Setup color picker yang dioptimalkan dengan debouncing dan cleanup yang tepat
        function setupOptimizedColorPicker(colorInputId, rangeInputId, opacityDisplayId, outputId, livewireTarget) {
            const colorInput = document.getElementById(colorInputId);
            const rangeInput = document.getElementById(rangeInputId);
            const opacityDisplay = document.getElementById(opacityDisplayId);
            const output = document.getElementById(outputId);

            if (!colorInput || !rangeInput || !output) return;

            // Buat fungsi update yang debounced untuk mencegah lag
            const debouncedUpdate = createDebouncedUpdate(livewireTarget, 300); // delay 300ms

            function updateRGBA(shouldUpdateLivewire = true) {
                const hex = colorInput.value;
                const opacity = rangeInput.value / 100;
                const rgb = hexToRgb(hex);

                if (rgb) {
                    // Gunakan HEX jika opacity 100%, RGBA jika kurang dari 100%
                    let finalValue;
                    if (opacity >= 0.99) { // 99% atau lebih = HEX
                        finalValue = hex.toUpperCase();
                    } else { // Kurang dari 99% = RGBA
                        finalValue = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${opacity})`;
                    }

                    output.value = finalValue;

                    // Update tampilan opacity
                    if (opacityDisplay) {
                        opacityDisplay.textContent = rangeInput.value + '%';
                    }

                    // ✅ CRITICAL: Update Livewire yang debounced untuk mencegah lag
                    if (shouldUpdateLivewire) {
                        debouncedUpdate(finalValue);
                    }
                }
            }

            // Bind events hanya sekali
            colorInput.addEventListener('input', () => updateRGBA(true));
            rangeInput.addEventListener('input', () => updateRGBA(true));

            // Inisialisasi tanpa panggilan Livewire
            updateRGBA(false);
        }

        // Buat fungsi debounced untuk mencegah terlalu banyak panggilan Livewire
        function createDebouncedUpdate(livewireTarget, delay) {
            return function(value) {
                // Clear timeout yang ada
                if (updateTimeouts.has(livewireTarget)) {
                    clearTimeout(updateTimeouts.get(livewireTarget));
                }

                // Set timeout baru
                const timeoutId = setTimeout(() => {
                    try {
                        @this.set(livewireTarget, value);
                        console.log('✅ Synced to Livewire:', livewireTarget, '=', value);
                    } catch (error) {
                        console.warn('⚠️ Update Livewire gagal untuk', livewireTarget, error);
                    }
                    updateTimeouts.delete(livewireTarget);
                }, delay);

                updateTimeouts.set(livewireTarget, timeoutId);
            };
        }


        // Konversi hex ke RGB yang dioptimalkan dengan caching
        const hexToRgbCache = new Map();

        function hexToRgb(hex) {
            if (!hex) return null;

            hex = hex.replace('#', '');

            if (hex.length === 3) {
                hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
            }

            if (hex.length !== 6) return null;

            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);

            if (isNaN(r) || isNaN(g) || isNaN(b)) return null;

            return {
                r,
                g,
                b
            };
        }

        // ===== UTILITY FUNCTIONS =====

        // Toggle tampilan deskripsi JSON
        function toggleJsonDescription() {
            const description = document.getElementById('jsonDescription');
            if (description) {
                if (description.classList.contains('hidden')) {
                    description.classList.remove('hidden');
                } else {
                    description.classList.add('hidden');
                }
            }
        }

        // Salin JSON dengan deskripsi ke clipboard
        function copyJsonToClipboard() {
            const jsonTextarea = document.querySelector('textarea[wire\\:model="stylesJson"]');
            if (jsonTextarea && jsonTextarea.value.trim()) {
                try {
                    // Parse JSON untuk memastikan valid
                    const jsonData = JSON.parse(jsonTextarea.value);

                    // Buat JSON dengan komentar deskripsi
                    const jsonWithComments = addCommentsToJson(jsonData);

                    navigator.clipboard.writeText(jsonWithComments).then(function() {
                        showToast('✅ JSON dengan deskripsi berhasil disalin! Tunjukkan ke AI untuk modifikasi.',
                            'success');
                    }, function(err) {
                        // Fallback untuk browser lama
                        const textArea = document.createElement('textarea');
                        textArea.value = jsonWithComments;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                        showToast('✅ JSON dengan deskripsi berhasil disalin! Tunjukkan ke AI untuk modifikasi.',
                            'success');
                    });
                } catch (error) {
                    // Jika JSON tidak valid, copy apa adanya
                    navigator.clipboard.writeText(jsonTextarea.value).then(function() {
                        showToast('JSON berhasil disalin (tanpa deskripsi karena format tidak valid)!', 'success');
                    }, function(err) {
                        jsonTextarea.select();
                        document.execCommand('copy');
                        showToast('JSON berhasil disalin (tanpa deskripsi karena format tidak valid)!', 'success');
                    });
                }
            } else {
                showToast('Tidak ada JSON untuk disalin. Harap tambahkan styling terlebih dahulu.', 'error');
            }
        }

        // Fungsi untuk menambahkan komentar deskripsi ke JSON
        function addCommentsToJson(jsonData) {
            return `Kamu adalah AI Designer yang membantu membuat tema visual untuk halaman linktree BPS.
Gunakan format JSON berikut sebagai template. User bisa memberikan instruksi perubahan tema.

🎯 ATURAN UTAMA:
1. Selalu keluarkan hasil dalam format JSON valid (tanpa komentar)
2. Jika user tidak menentukan parameter, gunakan default template
3. Konversi nama warna/hex ke format rgba() otomatis
4. Jangan tambahkan komentar atau teks di luar JSON
5. Pertahankan struktur JSON yang sama persis

📋 TEMPLATE LENGKAP DENGAN SEMUA OPSI:
${JSON.stringify(jsonData, null, 2)}

🎨 PANDUAN THEME CREATION:

📌 WARNA & FORMAT:
- Format WAJIB: HEX #RRGGBB (contoh: #FF0000, #00FF00, #0000FF)
- Semua warna menggunakan format HEX 6 digit
- Transparansi QR Code: 0-100% (diatur via slider color picker masing-masing)
- Background container: solid HEX colors
- Animasi warna: HEX colors untuk gradien dan effects
- Glow effect: HEX colors untuk shadow dan border effects

📌 UKURAN & SPACING:
- Font size judul: 20-32px (mobile friendly)
- Font size deskripsi: 13-18px
- Button border radius: 8-20px modern, 0-4px minimalis
- Container border radius: 16-32px modern, 0-8px minimalis
- Logo border radius: 50+ untuk circle, 8-20 untuk rounded square

📌 CONTAINER OPTIONS:
Struktur JSON:
"container": {
    "backgroundType": "solid" | "gradient",
    "backgroundColor": "#FFFFFF" (untuk solid, HEX format),
    "backgroundGradientStart": "#FFFFFF" (untuk gradient, HEX format),
    "backgroundGradientEnd": "#F8FAFC" (untuk gradient, HEX format),
    "backgroundGradientDirection": "0" - "360" (derajat, default: "135"),
    "borderRadius": "0" - "50" (px, default: "24"),
    "backdropBlur": "0" - "50" (px, default: "20"),
    "topGradientStart": "#FFD700" (warna gradien atas, HEX format),
    "topGradientEnd": "#002366" (warna gradien atas, HEX format),
    "topGradientHeight": "1" - "20" (px, default: "4")
}

Container Tips:
- Background Type: "solid" untuk warna polos, "gradient" untuk efek gradien
- Border Radius: 0-8px untuk minimalis, 16-32px untuk modern
- Backdrop Blur: 0-30px untuk effect kaca, 0 untuk performa
- Top Gradient: Garis aksen dekoratif di bagian atas container

📌 ANIMASI BACKGROUND (OPTIMIZED - NO LAG):
Struktur JSON:
"backgroundAnimation": {
    "type": "none" | "gradient" | "particles" | "waves",
    "speed": "2" - "10" (dalam detik, default: "5"),
    "intensity": "10" - "100" (dalam persen, default: "40"),
    "color1": "#002366" (warna utama animasi, HEX format),
    "color2": "#FFD700" (warna sekunder animasi, HEX format),
    "color3": "#1D4E5F" (warna tersier animasi, HEX format),
    "direction": "0" - "360" (dalam derajat, default: "0"),
    "size": "20" - "80" (ukuran elemen animasi, default: "40")
}

Tipe animasi:
- "none": Tanpa animasi (tercepat, untuk performa maksimal)
- "gradient": Gradien bergerak halus GPU-accelerated (elegan, modern, OPTIMIZED)
- "particles": Partikel subtle mengambang (playful, dynamic, OPTIMIZED - 6 particles)
- "waves": Gelombang lembut (organic, calming)

Tips performa optimal:
- Speed: 4-6 detik untuk smoothness terbaik
- Intensity: 30-60% untuk efek subtle yang tidak mengganggu
- Semua animasi menggunakan GPU acceleration (will-change, translateZ)
- Blur dikurangi untuk performa (gradient: 12-15px, particles: 1-1.5px)

📌 TEMA POPULER EXAMPLES:
- Corporate: biru navy + putih + emas, solid backgrounds
- Modern: gradients, high contrast, minimal borders
- Playful: bright colors, particles animation, rounded corners
- Elegant: muted tones, subtle gradients, thin borders
- Dark Mode: dark backgrounds, light text, neon accents

📌 KOMBINASI WARNA HARMONY:
- Monochromatic: satu warna dengan variasi terang-gelap
- Complementary: warna berseberangan (biru-orange, merah-hijau)
- Analogous: warna bersebelahan (biru-ungu-pink)
- Triadic: tiga warna dengan jarak sama (merah-kuning-biru)

📌 QR CODE STYLING OPTIONS:
Struktur JSON:
"qrcode": {
    "enabled": true | false,
    "position": "bottom-right" | "bottom-left" | "top-right" | "top-left",
    "size": "50" - "300" (dalam px, default: "200"),
    "borderRadius": "0" - "50" (dalam px, default: "12"),
    "padding": "0" - "50" (dalam px, default: "10"),
    "shadow": true | false,
    "hoverEffect": true | false,
    "showOnMobile": true | false,
    "tooltipText": "Scan QR untuk buka halaman ini",
    "backgroundColor": "#FFFFFF" (Background QR - BISA DI-STYLING!),
    "borderColor": "#FFFFFF" (Border Container - BISA DI-STYLING!),
    "darkColor": "#000000" (Pattern QR - BISA DI-STYLING!)
}

QR Code Tips:
- Position: pilih posisi strategis yang tidak mengganggu konten
- Size: 150-200px untuk desktop, 100-150px untuk mobile-friendly
- Colors:
  * backgroundColor: khusus untuk background QR code (bagian dalam QR)
  * borderColor: untuk border container QR (kotak luar)
  * darkColor: untuk pattern QR (garis-garis hitam QR)
- Transparansi: 0-100% (diatur via slider di color picker masing-masing, default: 100%)
- Border radius: 0-8px untuk sharp look, 12-20px untuk modern rounded
- Mobile: showOnMobile: false untuk menghindari clutter di layar kecil
- Independent Color Controls: backgroundColor, borderColor, dan darkColor masing-masing memiliki slider transparansi sendiri

QR Code Color Schemes (darkColor + backgroundColor + borderColor - HEX FORMAT):
- Classic: darkColor: "#000000", backgroundColor: "#FFFFFF", borderColor: "#FFFFFF"
- Navy Blue: darkColor: "#002366", backgroundColor: "#FFFFFF", borderColor: "#FFFFFF"
- Corporate: darkColor: "#1a365d", backgroundColor: "#F8FAFC", borderColor: "#F8FAFC"
- Red Alert: darkColor: "#dc2626", backgroundColor: "#FFFFFF", borderColor: "#FFFFFF"
- Green Forest: darkColor: "#059669", backgroundColor: "#FFFFFF", borderColor: "#FFFFFF"
- Purple Royal: darkColor: "#7c3aed", backgroundColor: "#FFFFFF", borderColor: "#FFFFFF"
- Dark Theme: darkColor: "#FFFFFF", backgroundColor: "#000000", borderColor: "#000000"
- Yellow Theme: darkColor: "#000000", backgroundColor: "#FFFF00", borderColor: "#FFFF00"
- Gold Luxury: darkColor: "#000000", backgroundColor: "#FFD700", borderColor: "#FFD700"
- Teal Modern: darkColor: "#4ECDC4", backgroundColor: "#FFFFFF", borderColor: "#FFFFFF"
- High Contrast: darkColor: "#000000", backgroundColor: "#000000", borderColor: "#000000"
- Soft Blue: darkColor: "#002366", backgroundColor: "#E0F2FE", borderColor: "#3B82F6"
- Warm Orange: darkColor: "#FF6B6B", backgroundColor: "#FFFFFF", borderColor: "#FB923C"
- Glass Effect: darkColor: "#002366", backgroundColor: "#FFFFFF", borderColor: "#FFFFFF" (70% opacity)

Note: backgroundColor, borderColor, dan darkColor masing-masing memiliki kontrol transparansi independen. Default: #FFFFFF (100% opacity) untuk background, #FFFFFF (70% opacity) untuk border, dan #000000 (70% opacity) untuk pattern.

Tugas kamu: Baca permintaan user, analisis tema yang diminta, lalu hasilkan JSON dengan perubahan yang sesuai. Sisanya tetap default template.

User: [PLACEHOLDER - GANTI DENGAN PERMINTAAN TEMA]`;
        }

        // Salin HTML ke clipboard
        function copyHtml() {
            const htmlCode = document.getElementById('htmlCode');
            if (htmlCode) {
                navigator.clipboard.writeText(htmlCode.value).then(function() {
                    showToast('✅ HTML berhasil disalin! Siap untuk GitHub Page Creator.', 'success');
                }, function(err) {
                    // Fallback for older browsers
                    try {
                        htmlCode.select();
                        document.execCommand('copy');
                        showToast('✅ HTML berhasil disalin! Siap untuk GitHub Page Creator.', 'success');
                    } catch (fallbackErr) {
                        showToast('❌ Gagal menyalin HTML. Silakan copy manual.', 'error');
                    }
                });
            }
        }

        // Tampilkan notifikasi toast yang dioptimalkan
        function showToast(message, type = 'success') {
            // Remove existing toasts to prevent spam
            const existingToasts = document.querySelectorAll('.toast-notification');
            existingToasts.forEach(toast => toast.remove());

            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? '✅' : '❌';

            toast.className =
                `toast-notification fixed bottom-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-2xl z-[9999] animate-fade-in max-w-sm`;
            toast.innerHTML = `
        <div class="flex items-center space-x-3">
            <span class="text-xl">${icon}</span>
            <div class="flex-1">
                <p class="text-sm font-semibold">${message}</p>
                <div class="h-1 mt-1 bg-white rounded-full bg-opacity-20">
                    <div class="h-1 bg-white rounded-full animate-progress" style="animation-duration: 4s;"></div>
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="font-bold text-white hover:text-gray-200" title="Close">×</button>
        </div>
    `;

            document.body.appendChild(toast);

            // Auto remove after 4 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.classList.add('animate-fade-out');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.remove();
                        }
                    }, 300);
                }
            }, 4000);

            // Add click to close
            toast.addEventListener('click', function(e) {
                if (e.target.tagName !== 'BUTTON') {
                    toast.remove();
                }
            });
        }

        // Debug function for development
        function debugRadioState() {
            const urlRadio = document.getElementById('logoUrl');
            const uploadRadio = document.getElementById('logoUpload');

            console.log('🔍 Radio State Debug:');
            console.log('URL Radio - checked:', urlRadio?.checked, 'disabled:', urlRadio?.disabled);
            console.log('Upload Radio - checked:', uploadRadio?.checked, 'disabled:', uploadRadio?.disabled);

            if (typeof @this !== 'undefined') {
                try {
                    console.log('Livewire State - useUploadedLogo:', @this.get('useUploadedLogo'));
                    console.log('Livewire State - hasUploadedImage:', @this.get('hasUploadedImage'));
                } catch (error) {
                    console.log('Could not access Livewire state:', error);
                }
            }
        }

        // TAMBAHAN: Force radio button to work freely (bypass semua logic)
        function forceRadioButtonFreedom() {
            console.log('🆓 FORCING COMPLETE RADIO BUTTON FREEDOM...');

            const urlRadio = document.getElementById('logoUrl');
            const uploadRadio = document.getElementById('logoUpload');

            if (urlRadio && uploadRadio) {
                // HAPUS SEMUA RESTRICTIONS
                urlRadio.disabled = false;
                uploadRadio.disabled = false;

                // HAPUS SEMUA VISUAL DISABLED CLASSES
                urlRadio.className = urlRadio.className.replace(/opacity-\d+|cursor-[\w-]+/g, '').trim();
                uploadRadio.className = uploadRadio.className.replace(/opacity-\d+|cursor-[\w-]+/g, '').trim();

                // FIX LABELS
                const urlLabel = urlRadio.nextElementSibling;
                const uploadLabel = uploadRadio.nextElementSibling;

                if (urlLabel) {
                    urlLabel.className = urlLabel.className.replace(/text-gray-\d+/g, '').trim() + ' text-gray-900';
                }
                if (uploadLabel) {
                    uploadLabel.className = uploadLabel.className.replace(/text-gray-\d+/g, '').trim() + ' text-gray-900';
                }

                console.log('✅ Radio buttons are now COMPLETELY FREE to toggle!');
                console.log('🎯 Click URL ⟷ Upload ⟷ URL - Works anytime!');
            }
        }

        // TAMBAHAN: Auto-fix function yang dipanggil otomatis
        function autoFixRadioButtons() {
            setTimeout(() => {
                console.log('🔧 Auto-enabling FREE TOGGLE...');
                forceRadioButtonFreedom();
            }, 1000);
        }

        // Expose debug function to global scope for console access
        window.debugRadioState = debugRadioState;
        window.forceRadioButtonFreedom = forceRadioButtonFreedom;
        window.autoFixRadioButtons = autoFixRadioButtons;

        console.log('🎉 Taut Pinang JavaScript fully loaded and optimized!');


        // ===== SORTABLE LINKS (DRAG & DROP REORDER) =====
        function initializeSortableLinks() {
            const container = document.getElementById('links-container');
            if (!container) return;

            console.log('🔄 Initializing sortable links...');

            let draggedItem = null;
            let draggedIndex = null;

            container.addEventListener('dragstart', function(e) {
                const linkItem = e.target.closest('.link-item');
                if (!linkItem) return;

                draggedItem = linkItem;
                draggedIndex = parseInt(linkItem.dataset.index);

                linkItem.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', linkItem.innerHTML);
            });

            container.addEventListener('dragend', function(e) {
                const linkItem = e.target.closest('.link-item');
                if (linkItem) {
                    linkItem.classList.remove('dragging');
                }

                // Get new order and sync with Livewire
                const newOrder = [];
                container.querySelectorAll('.link-item').forEach(item => {
                    newOrder.push(parseInt(item.dataset.index));
                });

                // Call Livewire method to reorder
                if (typeof Livewire !== 'undefined') {
                    @this.reorderLinks(newOrder);
                }
            });

            container.addEventListener('dragover', function(e) {
                e.preventDefault();
                const linkItem = e.target.closest('.link-item');
                if (!linkItem || linkItem === draggedItem) return;

                const bounding = linkItem.getBoundingClientRect();
                const offset = bounding.y + (bounding.height / 2);

                if (e.clientY - offset > 0) {
                    linkItem.after(draggedItem);
                } else {
                    linkItem.before(draggedItem);
                }
            });

            container.addEventListener('dragenter', function(e) {
                e.preventDefault();
            });

            console.log('✅ Sortable links initialized');
        }


        // ===== QR CODE COLOR SYNC SYSTEM =====
    </script>

    <!-- Complete CSS untuk Taut Pinang - Clean & Organized -->
    <style>
        /* ===== SORTABLE LINKS STYLES ===== */
        .link-item.dragging {
            opacity: 0.5;
            transform: scale(0.98);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .link-item {
            transition: all 0.2s ease;
        }

        /* ===== DARK MODE STYLES ===== */

        /* Header - Dark Mode */
        [data-theme="dark"] .text-gray-800 {
            color: #e5e7eb !important;
        }

        /* Header container background */
        [data-theme="dark"] [class*="px-4"][class*="py-6"],
        [data-theme="dark"] [class*="sm:px-6"],
        [data-theme="dark"] [class*="lg:px-8"] {
            background: rgba(17, 24, 39, 0.95) !important;
            border-bottom: 1px solid rgba(55, 65, 81, 0.3) !important;
        }

        /* Alert Messages - Dark Mode */
        [data-theme="dark"] .text-green-700 {
            color: #34d399 !important;
        }

        [data-theme="dark"] .bg-green-100 {
            background: rgba(16, 185, 129, 0.1) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        [data-theme="dark"] .text-red-700 {
            color: #f87171 !important;
        }

        [data-theme="dark"] .bg-red-100 {
            background: rgba(239, 68, 68, 0.1) !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
        }

        [data-theme="dark"] .text-blue-800 {
            color: #60a5fa !important;
        }

        [data-theme="dark"] .bg-blue-50 {
            background: rgba(59, 130, 246, 0.1) !important;
            border-color: rgba(59, 130, 246, 0.3) !important;
        }

        [data-theme="dark"] .text-blue-700 {
            color: #3b82f6 !important;
        }

        [data-theme="dark"] .text-blue-900 {
            color: #1d4ed8 !important;
        }

        /* Main Container - Dark Mode */
        [data-theme="dark"] .bg-white {
            background: rgba(17, 24, 39, 0.95) !important;
        }

        [data-theme="dark"] .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2) !important;
        }

        /* Form Sections - Dark Mode */
        [data-theme="dark"] .text-lg {
            color: #f3f4f6 !important;
        }

        [data-theme="dark"] .text-gray-500 {
            color: #9ca3af !important;
        }

        /* Info Box - Dark Mode */
        [data-theme="dark"] .border-blue-200 {
            border-color: rgba(59, 130, 246, 0.3) !important;
        }

        [data-theme="dark"] .bg-blue-100 {
            background: rgba(59, 130, 246, 0.1) !important;
        }

        [data-theme="dark"] .text-blue-600 {
            color: #3b82f6 !important;
        }

        /* Form Labels - Dark Mode */
        [data-theme="dark"] .text-gray-700 {
            color: #d1d5db !important;
        }

        [data-theme="dark"] .text-gray-600 {
            color: #9ca3af !important;
        }

        /* Form Controls - Dark Mode */
        [data-theme="dark"] .border-gray-300 {
            border-color: rgba(55, 65, 81, 0.5) !important;
        }

        [data-theme="dark"] .bg-gray-100 {
            background: rgba(31, 41, 55, 0.5) !important;
        }

        [data-theme="dark"] .bg-gray-50 {
            background: rgba(31, 41, 55, 0.3) !important;
        }

        /* Radio Buttons - Dark Mode */
        [data-theme="dark"] .text-blue-600 {
            color: #3b82f6 !important;
        }

        [data-theme="dark"] .text-gray-900 {
            color: #f3f4f6 !important;
        }

        /* Preview Elements - Dark Mode */
        [data-theme="dark"] .text-gray-400 {
            color: #9ca3af !important;
        }

        /* Input Fields - Dark Mode */
        [data-theme="dark"] input[type="text"],
        [data-theme="dark"] input[type="url"],
        [data-theme="dark"] input[type="number"],
        [data-theme="dark"] textarea,
        [data-theme="dark"] select {
            background: rgba(31, 41, 55, 0.8) !important;
            border-color: rgba(55, 65, 81, 0.5) !important;
            color: #f3f4f6 !important;
        }

        [data-theme="dark"] input[type="text"]:focus,
        [data-theme="dark"] input[type="url"]:focus,
        [data-theme="dark"] input[type="number"]:focus,
        [data-theme="dark"] textarea:focus,
        [data-theme="dark"] select:focus {
            border-color: rgba(59, 130, 246, 0.5) !important;
            background: rgba(31, 41, 55, 0.9) !important;
        }

        /* Buttons - Dark Mode */
        [data-theme="dark"] .text-gray-700 {
            color: #d1d5db !important;
        }

        [data-theme="dark"] .bg-gray-100:hover {
            background: rgba(55, 65, 81, 0.5) !important;
        }

        /* Preview Container - Dark Mode */
        [data-theme="dark"] .sticky .bg-white {
            background: rgba(17, 24, 39, 0.95) !important;
        }

        /* Upload Area - Dark Mode */
        [data-theme="dark"] .border-gray-200 {
            border-color: rgba(55, 65, 81, 0.3) !important;
        }

        [data-theme="dark"] .bg-yellow-50 {
            background: rgba(217, 119, 6, 0.1) !important;
            border-color: rgba(217, 119, 6, 0.3) !important;
        }

        [data-theme="dark"] .text-yellow-800 {
            color: #fbbf24 !important;
        }

        [data-theme="dark"] .text-yellow-700 {
            color: #f59e0b !important;
        }

        /* Color Picker Controls - Dark Mode */
        [data-theme="dark"] .border-gray-300 {
            border-color: rgba(55, 65, 81, 0.5) !important;
        }

        [data-theme="dark"] .bg-gray-50 {
            background: rgba(31, 41, 55, 0.3) !important;
        }

        /* Tab Navigation - Dark Mode */
        [data-theme="dark"] .text-gray-500 {
            color: #9ca3af !important;
        }

        [data-theme="dark"] .text-gray-700 {
            color: #d1d5db !important;
        }

        [data-theme="dark"] .text-blue-600 {
            color: #3b82f6 !important;
        }

        [data-theme="dark"] .bg-blue-50 {
            background: rgba(59, 130, 246, 0.1) !important;
        }

        /* Tab Content - Dark Mode */
        [data-theme="dark"] .text-gray-800 {
            color: #e5e7eb !important;
        }

        [data-theme="dark"] .text-gray-600 {
            color: #9ca3af !important;
        }

        /* Range Sliders - Dark Mode */
        [data-theme="dark"] .slider {
            background: linear-gradient(to right, #4b5563, #3b82f6) !important;
        }

        [data-theme="dark"] .slider::-webkit-slider-thumb {
            background: #3b82f6 !important;
            border: 1px solid rgba(59, 130, 246, 0.3) !important;
        }

        [data-theme="dark"] .slider::-moz-range-thumb {
            background: #3b82f6 !important;
            border: 1px solid rgba(59, 130, 246, 0.3) !important;
        }

        /* Mobile Preview - Dark Mode */
        [data-theme="dark"] .bg-gray-900 {
            background: #111827 !important;
        }

        /* Desktop Preview - Dark Mode */
        [data-theme="dark"] .bg-gray-200 {
            background: rgba(55, 65, 81, 0.5) !important;
        }

        [data-theme="dark"] .border-gray-300 {
            border-color: rgba(55, 65, 81, 0.5) !important;
        }

        [data-theme="dark"] .text-gray-400 {
            color: #9ca3af !important;
        }

        [data-theme="dark"] .bg-gray-700 {
            background: rgba(55, 65, 81, 0.8) !important;
        }

        /* Links - Dark Mode */
        [data-theme="dark"] .text-blue-900:hover {
            color: #60a5fa !important;
        }

        [data-theme="dark"] .hover\:text-blue-900:hover {
            color: #60a5fa !important;
        }

        /* Error Messages - Dark Mode */
        [data-theme="dark"] .text-red-500 {
            color: #f87171 !important;
        }

        /* Success Messages - Dark Mode */
        [data-theme="dark"] .text-green-600 {
            color: #34d399 !important;
        }

        /* Loading States - Dark Mode */
        [data-theme="dark"] .text-blue-600 {
            color: #3b82f6 !important;
        }

        /* Toast Notifications - Dark Mode */
        [data-theme="dark"] .toast-notification {
            background: rgba(31, 41, 55, 0.95) !important;
            border: 1px solid rgba(55, 65, 81, 0.3) !important;
        }

        /* Override untuk text-gray-800 di semua konteks */
        [data-theme="dark"] [class*="text-gray-800"] {
            color: #e5e7eb !important;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-out {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        @keyframes progress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* ===== ANIMATION CLASSES ===== */
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        .animate-fade-out {
            animation: fade-out 0.3s ease-out;
        }

        .animate-progress {
            animation: progress 4s linear;
        }

        .upload-loading-indicator {
            display: inline-block;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* ===== LAYOUT & POSITIONING ===== */
        html {
            scroll-behavior: smooth;
        }

        .z-\[9999\] {
            z-index: 9999;
        }

        .lg\:sticky {
            position: sticky;
            top: 6rem;
            z-index: 10;
        }

        [x-transition] {
            transition: all 0.3s ease;
        }

        /* ===== FORM CONTROLS ===== */

        /* Color Input Styling */
        input[type="color"] {
            -webkit-appearance: none;
            border: none;
            cursor: pointer;
        }

        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        input[type="color"]::-webkit-color-swatch {
            border: none;
            border-radius: 6px;
        }

        /* Range Slider Styling */
        .slider {
            -webkit-appearance: none;
            appearance: none;
            height: 8px;
            background: linear-gradient(to right, #e5e7eb, #3b82f6);
            border-radius: 4px;
            outline: none;
        }

        .slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #3b82f6;
            border-radius: 50%;
            cursor: pointer;
        }

        .slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #3b82f6;
            border-radius: 50%;
            cursor: pointer;
            border: none;
        }

        /* Radio Button Enhancements */
        input[type="radio"]:disabled+label {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Focus States */
        input[type="color"]:focus,
        input[type="range"]:focus,
        input[type="radio"]:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* ===== TOAST NOTIFICATIONS ===== */
        .toast-notification {
            transition: all 0.3s ease;
        }

        .toast-notification:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.26);
        }

        /* ===== MODAL STYLING ===== */
        #promptModal {
            backdrop-filter: blur(4px);
        }

        #promptModal .bg-white {
            max-height: 90vh;
            overflow-y: auto;
        }

        #customPromptText {
            font-family: 'Courier New', Monaco, monospace;
            line-height: 1.4;
            resize: vertical;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 640px) {
            .toast-notification {
                max-width: calc(100vw - 2rem);
                left: 1rem;
                right: 1rem;
            }

            #promptModal .max-w-2xl {
                max-width: 95%;
                margin: 1rem;
            }

            #customPromptText {
                font-size: 12px;
            }

            /* Mobile optimizations for form elements */
            .slider {
                height: 10px;
            }

            .slider::-webkit-slider-thumb {
                width: 24px;
                height: 24px;
            }

            .slider::-moz-range-thumb {
                width: 24px;
                height: 24px;
            }

            input[type="color"] {
                width: 40px;
                height: 40px;
                border-radius: 8px;
            }
        }

        @media (max-width: 768px) {

            /* Tablet optimizations */
            .lg\:sticky {
                position: relative;
                top: auto;
            }
        }

        /* ===== UTILITY CLASSES ===== */
        .transition-opacity {
            transition: opacity 0.3s ease;
        }

        .hover\:bg-blue-50:hover {
            background-color: #eff6ff;
        }

        .hover\:border-blue-400:hover {
            border-color: #60a5fa;
        }

        .cursor-not-allowed {
            cursor: not-allowed;
        }

        .opacity-50 {
            opacity: 0.5;
        }

        /* ===== PREVIEW ENHANCEMENTS ===== */
        #previewFrame {
            transition: opacity 0.3s ease;
        }

        #miniPreview {
            transition: opacity 0.3s ease, transform 0.2s ease;
        }

        #miniPreview:hover {
            transform: scale(1.1);
        }

        /* ===== LOADING STATES ===== */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* ===== ACCESSIBILITY IMPROVEMENTS ===== */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .slider {
                background: #000;
                border: 2px solid #fff;
            }

            input[type="color"] {
                border: 2px solid #000;
            }
        }

        /* Dark mode support (if needed) */
        @media (prefers-color-scheme: dark) {
            .toast-notification {
                box-shadow: 0 10px 25px rgba(255, 255, 255, 0.1);
            }
        }

        /* ===== PRINT STYLES ===== */
        @media print {

            .toast-notification,
            #promptModal,
            .lg\:sticky {
                display: none !important;
            }

            html {
                scroll-behavior: auto;
            }
        }

        /* ===== BROWSER SPECIFIC FIXES ===== */
        /* Firefox specific */
        @-moz-document url-prefix() {
            .slider {
                background: #e5e7eb;
            }
        }

        /* Safari specific */
        @supports (-webkit-appearance: none) {
            input[type="color"] {
                border-radius: 6px;
                overflow: hidden;
            }
        }

        /* ===== CUSTOM SCROLLBAR (WebKit) ===== */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* ===== COMPONENT SPECIFIC STYLES ===== */

        /* Radio button container enhancements */
        .radio-container {
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .radio-container:hover {
            background-color: rgba(59, 130, 246, 0.05);
            border-color: rgba(59, 130, 246, 0.3);
        }

        /* Upload area styling */
        .upload-area {
            transition: all 0.3s ease;
            border: 2px dashed #d1d5db;
        }

        .upload-area:hover {
            border-color: #3b82f6;
            background-color: #f8faff;
        }

        .upload-area.dragover {
            border-color: #1d4ed8;
            background-color: #dbeafe;
            transform: scale(1.02);
        }

        /* Preview container */
        .preview-container {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .preview-container:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.26);
        }

        /* Success/Error states */
        .state-success {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .state-error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .state-warning {
            border-color: #f59e0b;
            background-color: #fffbeb;
        }

        /* Loading spinner */
        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ===== PERFORMANCE OPTIMIZATIONS ===== */
        * {
            box-sizing: border-box;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        /* GPU acceleration for animations */
        .animate-fade-in,
        .animate-fade-out,
        .toast-notification {
            will-change: transform, opacity;
            transform: translateZ(0);
            backface-visibility: hidden;
        }

        .slider-green::-webkit-slider-thumb {
            appearance: none;
            width: 16px;
            height: 16px;
            background: #10b981;
            cursor: pointer;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.33);
        }

        .slider-green::-moz-range-thumb {
            width: 16px;
            height: 16px;
            background: #10b981;
            cursor: pointer;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.33);
            border: none;
        }

        .slider-green::-webkit-slider-thumb:hover {
            background: #059669;
            transform: scale(1.1);
        }

        .slider-green::-moz-range-thumb:hover {
            background: #059669;
            transform: scale(1.1);
        }

        /* Button Animation System - Toggle Controlled */
        .button-animation-enabled {
            position: relative;
            overflow: hidden;
        }

        .button-animation-enabled::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
            pointer-events: none;
        }

        .button-animation-enabled:active::before {
            width: 300px;
            height: 300px;
        }

        /* Button Glow Effect */
        .save-button.glow-enabled::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #3b82f6, #10b981, #f59e0b, #3b82f6);
            border-radius: inherit;
            filter: blur(8px);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .save-button.glow-enabled:hover::after {
            opacity: 0.6;
        }

        /* Disabled state animations */
        .button-animation-enabled:disabled {
            cursor: not-allowed;
            transform: none !important;
        }

        .button-animation-enabled:disabled::before {
            display: none;
        }
    </style>
</div>
