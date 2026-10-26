<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Tautan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BuatTautan extends Component
{
    use WithFileUploads;

    public $judul = '';
    public $deskripsi = '';
    public $slug = '';
    public $links = [];
    public $logoUrl = '';
    public $footerText1 = '';
    public $footerText2 = '';
    public $generatedHtml = '';
    public $previewHtml = '';

    // QR Color Picker Properties untuk Livewire
    public $qrBackgroundColorPicker = '#ffffff';
    public $qrBorderColorPicker = '#ffffff';
    public $qrDarkColorPicker = '#000000';
    public $qrBackgroundOpacity = 100;
    public $qrBorderOpacity = 100;
    public $qrDarkOpacity = 100;

    // Image upload properties with fixes
    public $logoUpload;
    public $useUploadedLogo = false; // false = URL, true = Upload
    public $logoPreviewUrl = ''; // Fixed: Better preview URL handling
    public $uploadedLogoPath = ''; // Temporary path for cleanup

    // TAMBAHAN: Flag untuk mengontrol input file
    public $hasUploadedImage = false; // Track apakah sudah ada gambar yang diupload

    // Enhanced styling properties with QR code settings
    public $styles = [
        'background' => [
            'gradientStart' => '#002366',
            'gradientEnd' => '#1D4E5F',
            'direction' => '135'
        ],
        'container' => [
            'backgroundType' => 'solid',
            'backgroundColor' => '#FFFFFF',
            'backgroundGradientStart' => '#FFFFFF',
            'backgroundGradientEnd' => '#F8FAFC',
            'backgroundGradientDirection' => '135',
            'borderRadius' => '24',
            'backdropBlur' => '20',
            'topGradientStart' => '#FFD700',
            'topGradientEnd' => '#002366',
            'topGradientHeight' => '4'
        ],
        'title' => [
            'fontSize' => '26',
            'fontWeight' => '700',
            'color' => '#002366'
        ],
        'description' => [
            'color' => '#666666',
            'fontSize' => '15'
        ],
        'button' => [
            'backgroundColor' => '#FFFFFF',
            'color' => '#002366',
            'borderRadius' => '12',
            'borderColor' => '#EAEAEA',
            'borderWidth' => '2',
            'borderStyle' => 'solid'
        ],
        'buttonHover' => [
            'backgroundStart' => '#FFD700',
            'backgroundStartOpacity' => 100,
            'backgroundEnd' => '#FFFFFF',
            'backgroundEndOpacity' => 100,
            'color' => '#002366',
            'glowColor' => '#FFD700',
            'glowBlur' => '30',
            'borderColor' => '#FFD700',
            'borderWidth' => '2',
            'borderStyle' => 'solid'
        ],
        'logo' => [
            'backgroundType' => 'solid',
            'backgroundColor' => '#FFFFFF',
            'backgroundGradientStart' => '#FFFFFF',
            'backgroundGradientEnd' => '#F8FAFC',
            'backgroundGradientDirection' => '135',
            'borderColor' => '#FFD700',
            'borderWidth' => '3',
            'borderStyle' => 'solid',
            'borderRadius' => '50'
        ],
        'footer' => [
            'color' => '#999999',
            'fontSize' => '12',
            'borderColor' => '#EEEEEE',
            'marginTop' => '30',
            'paddingTop' => '20'
        ],
        'qrcode' => [
            'enabled' => true,
            'position' => 'bottom-right',
            'size' => '200',
            'borderRadius' => '12',
            'padding' => '10',
            'showOnMobile' => false,
            'tooltipText' => 'Scan QR untuk buka halaman ini',
            // ✅ 3 KONTROL WARNA TERPISAH - DEFAULT PUTIH-HITAM
            'backgroundColor' => '#ffffff',    // Background QR - PUTIH
            'borderColor' => '#ffffff',        // Border Container - PUTIH
            'darkColor' => '#000000'           // Pattern QR - HITAM
        ]
    ];

    public $stylesJson = '';

    // Icon picker
    public $showIconPicker = false;
    public $selectedLinkIndex = null;

    protected $rules = [
        'judul' => 'required|min:3|max:100',
        'slug' => 'required|min:3|max:50|regex:/^[a-zA-Z0-9\-_]+$/',
        'deskripsi' => 'nullable|max:500',
        'logoUrl' => 'nullable|url',
        'logoUpload' => 'nullable|image|max:2048', // max 2MB
        'footerText1' => 'nullable|max:100',
        'footerText2' => 'nullable|max:100',
        'links.*.judul' => 'required|min:1|max:100',
        'links.*.url' => 'required|url',
    ];

    protected $messages = [
        'judul.required' => 'Judul tautan wajib diisi',
        'judul.min' => 'Judul minimal 3 karakter',
        'judul.max' => 'Judul maksimal 100 karakter',
        'slug.required' => 'Slug wajib diisi',
        'slug.min' => 'Slug minimal 3 karakter',
        'slug.max' => 'Slug maksimal 50 karakter',
        'slug.regex' => 'Slug hanya boleh berisi huruf, angka, tanda hubung (-) dan underscore (_)',
        'deskripsi.max' => 'Deskripsi maksimal 500 karakter',
        'logoUrl.url' => 'Format URL logo tidak valid',
        'logoUpload.image' => 'File harus berupa gambar',
        'logoUpload.max' => 'Ukuran gambar maksimal 2MB',
        'footerText1.max' => 'Footer baris 1 maksimal 100 karakter',
        'footerText2.max' => 'Footer baris 2 maksimal 100 karakter',
        'links.*.judul.required' => 'Judul link wajib diisi',
        'links.*.judul.max' => 'Judul link maksimal 100 karakter',
        'links.*.url.required' => 'URL wajib diisi',
        'links.*.url.url' => 'Format URL tidak valid',
    ];

    public function mount()
    {
        $this->initializeDefaultData();
        $this->stylesJson = json_encode($this->styles, JSON_PRETTY_PRINT);

        // EXPLICIT default values - PENTING!
        $this->useUploadedLogo = false;    // DEFAULT: URL mode
        $this->hasUploadedImage = false;   // DEFAULT: no uploaded image
        $this->logoPreviewUrl = '';        // DEFAULT: empty
        $this->logoUpload = null;          // DEFAULT: no file

        // ✅ Initialize QR Color Picker values from default styles
        $this->initializeQRColorPickers();

        logger('=== MOUNT CLEAN ===');
        logger('All flags set to default - free toggle enabled');
        logger('🎨 Initial QR Border Color: ' . $this->styles['qrcode']['borderColor']);
        logger('🎨 Initial QR Dark Color: ' . $this->styles['qrcode']['darkColor']);

        $this->generatePreview();
    }


    private function initializeDefaultData()
    {
        $this->judul = 'BPS Kota Tanjungpinang';
        $this->deskripsi = 'Badan Pusat Statistik Kota Tanjungpinang - Satu Data Untuk Semua';
        $this->slug = 'bps-tanjungpinang';

        $this->footerText1 = '© 2025 Made with ❤ BPS Kota Tanjungpinang -';
        $this->footerText2 = 'BPS Provinsi Kepulauan Riau';

        $this->links = [
            [
                'judul' => 'Website Resmi BPS',
                'url' => 'https://tanjungpinangkota.bps.go.id',
                'icon' => '', // Font Awesome icon class - kosong secara default
                'active' => true, // Active state
                'enableCustomStyling' => false, // DEFAULT: DISABLED - gunakan global style
                'backgroundColor' => '#FFFFFF', // Default white background (hanya digunakan jika enableCustomStyling = true)
                'textColor' => '#002366'          // Default blue text (hanya digunakan jika enableCustomStyling = true)
            ],
            [
                'judul' => 'Data dan Statistik',
                'url' => 'https://tanjungpinangkota.bps.go.id/subject.html',
                'icon' => '',
                'active' => true,
                'enableCustomStyling' => false, // DEFAULT: DISABLED - gunakan global style
                'backgroundColor' => '#FFFFFF',
                'textColor' => '#002366'
            ],
            [
                'judul' => 'Berita Resmi',
                'url' => 'https://tanjungpinangkota.bps.go.id/news.html',
                'icon' => '',
                'active' => true,
                'enableCustomStyling' => false, // DEFAULT: DISABLED - gunakan global style
                'backgroundColor' => '#FFFFFF',
                'textColor' => '#002366'
            ]
        ];

        $this->logoUrl = 'https://i.pinimg.com/474x/e4/8a/b2/e48ab2e2e8055f15f04caf6968c1314a.jpg';

        // PERBAIKAN: Pastikan default state yang benar
        $this->useUploadedLogo = false;  // Default ke URL mode
        $this->logoPreviewUrl = '';
        $this->hasUploadedImage = false;

        // TAMBAHAN: Reset upload-related vars
        $this->logoUpload = null;
        $this->uploadedLogoPath = '';
    }

    /**
     * Initialize QR Color Picker values from default styles
     */
    private function initializeQRColorPickers()
    {
        // Get default QR colors from styles
        $qrStyles = $this->styles['qrcode'];

        // Extract hex colors from rgba values (default styles use rgba)
        $bgColor = $qrStyles['backgroundColor'] ?? '#ffffff';
        $borderColor = $qrStyles['borderColor'] ?? '#ffffff';
        $darkColor = $qrStyles['darkColor'] ?? '#000000';

        // Convert to hex if rgba format
        $this->qrBackgroundColorPicker = $this->rgbaToHex($bgColor);
        $this->qrBorderColorPicker = $this->rgbaToHex($borderColor);
        $this->qrDarkColorPicker = $this->rgbaToHex($darkColor);

        // Set default opacity to 100%
        $this->qrBackgroundOpacity = 100;
        $this->qrBorderOpacity = 100;
        $this->qrDarkOpacity = 100;

        logger('🎨 QR Color Pickers initialized:');
        logger('   Background: ' . $this->qrBackgroundColorPicker);
        logger('   Border: ' . $this->qrBorderColorPicker);
        logger('   Dark: ' . $this->qrDarkColorPicker);
    }

    /**
     * Convert rgba to hex for color picker initialization
     */
    private function rgbaToHex($color)
    {
        if (strpos($color, 'rgba') === 0) {
            // Extract RGB from rgba(r, g, b, a)
            preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+)/', $color, $matches);
            if (count($matches) >= 4) {
                $r = intval($matches[1]);
                $g = intval($matches[2]);
                $b = intval($matches[3]);
                return sprintf("#%02x%02x%02x", $r, $g, $b);
            }
        } elseif (strpos($color, 'rgb') === 0) {
            // Extract RGB from rgb(r, g, b)
            preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)/', $color, $matches);
            if (count($matches) >= 4) {
                $r = intval($matches[1]);
                $g = intval($matches[2]);
                $b = intval($matches[3]);
                return sprintf("#%02x%02x%02x", $r, $g, $b);
            }
        }

        // Return as-is if already hex or other format
        return $color;
    }

    /**
     * Process color with opacity to create RGBA format
     */
    private function processColorWithOpacity($color, $opacity = 100)
    {
        // Remove # if present
        $hexColor = ltrim($color, '#');

        // Convert hex to RGB
        if (strlen($hexColor) === 6) {
            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));

            // Convert opacity percentage to decimal (0-1)
            $alpha = $opacity / 100;

            // Return RGBA format
            return "rgba({$r}, {$g}, {$b}, {$alpha})";
        }

        // Return as-is if not a valid hex color
        return $color;
    }
    // FIXED: Handle logo upload with multiple fallback methods
    public function updatedLogoUpload()
    {
        $this->validate([
            'logoUpload' => 'image|max:2048',
        ]);

        if ($this->logoUpload) {
            try {
                logger('=== UPLOAD START ===');

                // Reset preview URL
                $this->logoPreviewUrl = '';

                // Try different methods untuk generate preview
                try {
                    $this->logoPreviewUrl = $this->logoUpload->temporaryUrl();
                    logger('Preview generated with temporaryUrl');
                } catch (\Exception $e) {
                    logger('temporaryUrl failed, trying base64');
                    if ($this->logoUpload->getSize() < 500000) {
                        $imageData = base64_encode($this->logoUpload->get());
                        $mimeType = $this->logoUpload->getMimeType();
                        $this->logoPreviewUrl = "data:{$mimeType};base64,{$imageData}";
                        logger('Preview generated with base64');
                    }
                }

                if (!empty($this->logoPreviewUrl)) {
                    // PERBAIKAN: Set flags dengan benar
                    $this->hasUploadedImage = true;
                    $this->useUploadedLogo = true;  // Auto switch ke upload mode

                    $this->generatePreview();
                    session()->flash('success', 'Logo berhasil diupload!');
                    logger('Upload successful - flags set correctly');
                } else {
                    throw new \Exception('Gagal generate preview URL');
                }
            } catch (\Exception $e) {
                // Reset flags jika upload gagal
                $this->hasUploadedImage = false;
                session()->flash('error', 'Gagal upload: ' . $e->getMessage());
                logger('Upload failed: ' . $e->getMessage());
            }
        }

        $this->dispatch('logo-uploaded',
            logoPreviewUrl: $this->logoPreviewUrl ?: '',
            fileName: $this->logoUpload ? $this->logoUpload->getClientOriginalName() : ''
        );
    }
    public function updatedUseUploadedLogo()
    {
        // PERBAIKAN: HILANGKAN SEMUA BLOCKING LOGIC!
        // Allow toggle bebas tanpa restrict apapun

        logger('=== TOGGLE BEBAS ===');
        logger('useUploadedLogo changed to: ' . ($this->useUploadedLogo ? 'true (Upload)' : 'false (URL)'));
        logger('hasUploadedImage: ' . ($this->hasUploadedImage ? 'true' : 'false'));

        // Generate preview sesuai mode baru - NO BLOCKING
        $this->generatePreview();

        // Kasih pesan informatif aja, tidak ada blocking
        $message = $this->useUploadedLogo
            ? 'Beralih ke mode Upload Gambar'
            : 'Beralih ke mode Link URL';

        session()->flash('success', $message);
        logger('Toggle successful: ' . $message);
    }

    // PERBAIKAN: Get current logo source with better handling
    public function getCurrentLogoSource()
    {
        // Log untuk debugging
        logger('getCurrentLogoSource - useUploadedLogo: ' . ($this->useUploadedLogo ? 'true' : 'false'));
        logger('getCurrentLogoSource - logoPreviewUrl: ' . ($this->logoPreviewUrl ?: 'empty'));
        logger('getCurrentLogoSource - logoUrl: ' . ($this->logoUrl ?: 'empty'));

        if ($this->useUploadedLogo && !empty($this->logoPreviewUrl)) {
            // Validate URL before returning
            if (filter_var($this->logoPreviewUrl, FILTER_VALIDATE_URL) || strpos($this->logoPreviewUrl, 'data:') === 0) {
                return $this->logoPreviewUrl;
            } else {
                logger('Invalid logoPreviewUrl, falling back to logoUrl');
            }
        }

        return $this->logoUrl ?: 'https://i.pinimg.com/474x/e4/8a/b2/e48ab2e2e8055f15f04caf6968c1314a.jpg';
    }

    // PERBAIKAN: Clear uploaded logo and reset ke URL mode
    public function clearUploadedLogo()
    {
        logger('=== CLEAR UPLOAD ===');

        // Clean up files
        if (!empty($this->uploadedLogoPath) && Storage::disk('public')->exists($this->uploadedLogoPath)) {
            Storage::disk('public')->delete($this->uploadedLogoPath);
        }

        // Reset semua properties
        $this->logoUpload = null;
        $this->logoPreviewUrl = '';
        $this->uploadedLogoPath = '';
        $this->hasUploadedImage = false;
        $this->useUploadedLogo = false;  // Auto kembali ke URL

        $this->generatePreview();
        session()->flash('success', 'Gambar dihapus. Kembali ke mode URL.');
        logger('Upload cleared - all flags reset');
    }

    public function clearForm()
    {
        // Clean up any temporary files
        if (!empty($this->uploadedLogoPath) && Storage::disk('public')->exists($this->uploadedLogoPath)) {
            Storage::disk('public')->delete($this->uploadedLogoPath);
        }

        $this->reset([
            'judul',
            'deskripsi',
            'slug',
            'links',
            'footerText1',
            'footerText2',
            'generatedHtml',
            'previewHtml',
            'logoUpload',
            'logoPreviewUrl',
            'uploadedLogoPath',
            'useUploadedLogo',
            'hasUploadedImage'
        ]);

        $this->resetStylesToDefault();
        $this->logoUrl = 'https://i.pinimg.com/474x/e4/8a/b2/e48ab2e2e8055f15f04caf6968c1314a.jpg';
        $this->useUploadedLogo = false; // PERBAIKAN: Pastikan default ke URL
        $this->hasUploadedImage = false;
        $this->addLink();
        $this->generatePreview();

        session()->flash('success', 'Form berhasil dikosongkan!');
    }

    public function clearStyles()
    {
        $this->resetStylesToDefault();
        $this->generatePreview();
        session()->flash('success', 'Styles berhasil direset ke default!');
    }

    private function resetStylesToDefault()
    {
        $this->styles = [
            'background' => [
                'gradientStart' => '#002366',
                'gradientEnd' => '#1D4E5F',
                'direction' => '135'
            ],
            'container' => [
                'backgroundType' => 'solid',
                'backgroundColor' => '#FFFFFF',
                'backgroundGradientStart' => '#FFFFFF',
                'backgroundGradientEnd' => '#F8FAFC',
                'backgroundGradientDirection' => '135',
                'borderRadius' => '24',
                'backdropBlur' => '20',
                'topGradientStart' => '#FFD700',
                'topGradientEnd' => '#002366',
                'topGradientHeight' => '4'
            ],
            'title' => [
                'fontSize' => '26',
                'fontWeight' => '700',
                'color' => '#002366'
            ],
            'description' => [
                'color' => '#666666',
                'fontSize' => '15'
            ],
            'button' => [
                'backgroundColor' => '#FFFFFF',
                'color' => '#002366',
                'borderRadius' => '12',
                'borderColor' => '#EAEAEA',
                'borderWidth' => '2',
                'borderStyle' => 'solid'
            ],
            'buttonHover' => [
                'backgroundStart' => '#FFD700',
                'backgroundStartOpacity' => 100,
                'backgroundEnd' => '#FFFFFF',
                'backgroundEndOpacity' => 100,
                'color' => '#002366',
                'glowColor' => '#FFD700',
                'glowBlur' => '30',
                'borderColor' => '#FFD700',
                'borderWidth' => '2',
                'borderStyle' => 'solid'
            ],
            'logo' => [
                'backgroundType' => 'solid',
                'backgroundColor' => '#FFFFFF',
                'backgroundGradientStart' => '#FFFFFF',
                'backgroundGradientEnd' => '#F8FAFC',
                'backgroundGradientDirection' => '135',
                'borderColor' => '#FFD700',
                'borderWidth' => '3',
                'borderStyle' => 'solid',
                'borderRadius' => '50'
            ],
            'footer' => [
                'color' => '#999999',
                'fontSize' => '12',
                'borderColor' => '#EEEEEE',
                'marginTop' => '30',
                'paddingTop' => '20'
            ],
            'qrcode' => [
                'enabled' => true,
                'position' => 'bottom-right',
                'size' => '200',
                'borderRadius' => '12',
                'padding' => '10',
                'showOnMobile' => false,
                'tooltipText' => 'Scan QR untuk buka halaman ini',
                // ✅ 3 KONTROL WARNA TERPISAH - DEFAULT PUTIH-HITAM
                'backgroundColor' => '#ffffff',    // Background QR - PUTIH
                'borderColor' => '#ffffff',        // Border Container - PUTIH
                'darkColor' => '#000000'           // Pattern QR - HITAM
            ]
        ];

        $this->stylesJson = json_encode($this->styles, JSON_PRETTY_PRINT);
    }

    public function loadSampleData()
    {
        $this->initializeDefaultData();
        $this->generatePreview();
        session()->flash('success', 'Data contoh berhasil dimuat!');
    }

    // Cleanup on component lifecycle
    public function dehydrate()
    {
        // Clean up old temporary files on each request
        $this->cleanupOldTempFiles();
    }

    private function cleanupOldTempFiles()
    {
        try {
            $tempDir = 'public/temp/previews';
            if (Storage::exists($tempDir)) {
                $files = Storage::files($tempDir);
                $now = now();

                foreach ($files as $file) {
                    // Delete files older than 1 hour
                    if ($now->diffInHours(Storage::lastModified($file)) > 1) {
                        Storage::delete($file);
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently handle cleanup errors
            logger('Temp file cleanup error: ' . $e->getMessage());
        }
    }

    // Updated methods...
    public function updatedJudul()
    {
        try {
            if (empty($this->slug) || $this->slug === $this->generateSlugFromTitle($this->judul)) {
                $this->slug = $this->generateSlugFromTitle($this->judul);
            }
            $this->generatePreview();
        } catch (\Exception $e) {
            logger('Error updating judul: ' . $e->getMessage());
        }
    }
    // BARU: Method untuk generate slug dari title tanpa menghapus trailing chars
    private function generateSlugFromTitle($title)
    {
        if (empty($title)) {
            return '';
        }

        // Convert ke slug
        $baseSlug = Str::slug($title);

        // Generate unique slug
        return $this->generateUniqueSlug($baseSlug);
    }

    /**
     * Generate unique slug by checking database
     */
    private function generateUniqueSlug($baseSlug)
    {
        $originalSlug = $baseSlug;
        $counter = 1;

        // Check if slug exists (including soft deletes)
        while (Tautan::withTrashed()->where('slug', $baseSlug)->exists()) {
            $baseSlug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $baseSlug;
    }

    public function updatedSlug()
    {
        try {
            // PERBAIKAN: Jangan langsung hapus trailing characters
            $this->slug = $this->cleanSlugGently($this->slug);
            $this->generatePreview();
        } catch (\Exception $e) {
            logger('Error updating slug: ' . $e->getMessage());
        }
    }
    // BARU: Method untuk clean slug dengan lembut
    private function cleanSlugGently($slug)
    {
        if (empty($slug)) {
            return '';
        }

        // Cek apakah user sedang mengetik (ending dengan - atau _)
        $isTyping = str_ends_with($slug, '-') || str_ends_with($slug, '_');

        if ($isTyping) {
            // Jika sedang mengetik, hanya bersihkan karakter invalid tapi pertahankan trailing
            $cleaned = preg_replace('/[^a-zA-Z0-9\-_]/', '', $slug);
            // Hapus multiple dashes/underscores berturut-turut tapi pertahankan yang terakhir
            $cleaned = preg_replace('/[-_]{2,}/', '-', $cleaned);
            return strtolower($cleaned);
        } else {
            // Jika tidak sedang mengetik, bersihkan normal
            return Str::slug($slug);
        }
    }
    // BARU: Method untuk validasi final slug (dipanggil saat submit)
    private function getFinalSlug()
    {
        $baseSlug = rtrim(Str::slug($this->slug), '-_');

        // Generate unique slug untuk memastikan tidak duplikat
        return $this->generateUniqueSlug($baseSlug);
    }


    public function updatedDeskripsi()
    {
        $this->generatePreview();
    }

    public function updatedLogoUrl()
    {
        $this->generatePreview();
    }

    public function updatedFooterText1()
    {
        $this->generatePreview();
    }

    public function updatedFooterText2()
    {
        $this->generatePreview();
    }

    public function updatedLinks()
    {
        $this->generatePreview();
    }

    // Di method updatedStyles()
    public function updatedStyles()
    {
        try {
            // Debug: Log QR color changes
            if (isset($this->styles['qrcode']['borderColor'])) {
                logger('🎨 QR Border Color updated: ' . $this->styles['qrcode']['borderColor']);
            }
            if (isset($this->styles['qrcode']['darkColor'])) {
                logger('🎨 QR Dark Color updated: ' . $this->styles['qrcode']['darkColor']);
            }

            $this->stylesJson = json_encode($this->styles, JSON_PRETTY_PRINT);
            $this->generatePreview();
        } catch (\Exception $e) {
            logger('Error updating styles: ' . $e->getMessage());
        }
    }

    public function applyJsonStyles()
    {
        try {
            $decodedStyles = json_decode($this->stylesJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                session()->flash('error', 'Invalid JSON format: ' . json_last_error_msg());
                return;
            }

            if (!is_array($decodedStyles)) {
                session()->flash('error', 'JSON must be a valid object.');
                return;
            }

            $this->styles = $this->deepMergeArrays($this->styles, $decodedStyles);
            $this->stylesJson = json_encode($this->styles, JSON_PRETTY_PRINT);
            $this->generatePreview();

            $this->dispatch('stylesUpdated');  // Sudah benar

            session()->flash('success', 'JSON styles berhasil diterapkan!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error applying JSON: ' . $e->getMessage());
        }
    }

    private function deepMergeArrays($array1, $array2)
    {
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key])) {
                $array1[$key] = $this->deepMergeArrays($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }

    public function updatedStylesJson()
    {
        try {
            $decodedStyles = json_decode($this->stylesJson, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedStyles)) {
                $this->styles = $this->deepMergeArrays($this->styles, $decodedStyles);
                $this->generatePreview();
                $this->dispatch('stylesUpdated');
            }
        } catch (\Exception $e) {
            // Invalid JSON, keep current styles
        }
    }

    /**
     * Special method to handle QR color updates from Livewire
     */
    public function updatedStylesQrcodeBorderColor()
    {
        logger('🎨 QR Border Color updated via Livewire: ' . $this->styles['qrcode']['borderColor']);
        $this->generatePreview();
    }

    /**
     * Special method to handle QR dark color updates from Livewire
     */
    public function updatedStylesQrcodeDarkColor()
    {
        logger('🎨 QR Dark Color updated via Livewire: ' . $this->styles['qrcode']['darkColor']);
        $this->generatePreview();
    }

    // ✅ QR COLOR PICKER LIVEWIRE UPDATE METHODS
    public function updatedQrBackgroundColorPicker()
    {
        logger('🎨 QR Background Color Picker updated: ' . $this->qrBackgroundColorPicker);
        $this->updateQRColorsFromPickers();
    }

    public function updatedQrBackgroundOpacity()
    {
        logger('🎨 QR Background Opacity updated: ' . $this->qrBackgroundOpacity);
        $this->updateQRColorsFromPickers();
    }

    public function updatedQrBorderColorPicker()
    {
        logger('🎨 QR Border Color Picker updated: ' . $this->qrBorderColorPicker);
        $this->updateQRColorsFromPickers();
    }

    public function updatedQrBorderOpacity()
    {
        logger('🎨 QR Border Opacity updated: ' . $this->qrBorderOpacity);
        $this->updateQRColorsFromPickers();
    }

    public function updatedQrDarkColorPicker()
    {
        logger('🎨 QR Dark Color Picker updated: ' . $this->qrDarkColorPicker);
        $this->updateQRColorsFromPickers();
    }

    public function updatedQrDarkOpacity()
    {
        logger('🎨 QR Dark Opacity updated: ' . $this->qrDarkOpacity);
        $this->updateQRColorsFromPickers();
    }

    // ===== BUTTON NORMAL SETTINGS - AUTO UPDATE PREVIEW =====
    public function updatedStylesButtonBackgroundColor()
    {
        logger('🎨 Button Background Color updated: ' . ($this->styles['button']['backgroundColor'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonColor()
    {
        logger('🎨 Button Text Color updated: ' . ($this->styles['button']['color'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonBorderColor()
    {
        logger('🎨 Button Border Color updated: ' . ($this->styles['button']['borderColor'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonBorderWidth()
    {
        logger('🎨 Button Border Width updated: ' . ($this->styles['button']['borderWidth'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonBorderStyle()
    {
        logger('🎨 Button Border Style updated: ' . ($this->styles['button']['borderStyle'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonBorderRadius()
    {
        logger('🎨 Button Border Radius updated: ' . ($this->styles['button']['borderRadius'] ?? 'not set'));
        $this->generatePreview();
    }

    // ===== BUTTON HOVER SETTINGS - AUTO UPDATE PREVIEW =====
    public function updatedStylesButtonHoverBackgroundStart()
    {
        logger('🎨 Button Hover Background Start updated: ' . ($this->styles['buttonHover']['backgroundStart'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonHoverBackgroundEnd()
    {
        logger('🎨 Button Hover Background End updated: ' . ($this->styles['buttonHover']['backgroundEnd'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonHoverColor()
    {
        logger('🎨 Button Hover Text Color updated: ' . ($this->styles['buttonHover']['color'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonHoverBorderColor()
    {
        logger('🎨 Button Hover Border Color updated: ' . ($this->styles['buttonHover']['borderColor'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonHoverBorderWidth()
    {
        logger('🎨 Button Hover Border Width updated: ' . ($this->styles['buttonHover']['borderWidth'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonHoverBorderStyle()
    {
        logger('🎨 Button Hover Border Style updated: ' . ($this->styles['buttonHover']['borderStyle'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonHoverGlowColor()
    {
        logger('🎨 Button Hover Glow Color updated: ' . ($this->styles['buttonHover']['glowColor'] ?? 'not set'));
        $this->generatePreview();
    }

    public function updatedStylesButtonHoverGlowBlur()
    {
        logger('🎨 Button Hover Glow Blur updated: ' . ($this->styles['buttonHover']['glowBlur'] ?? 'not set'));
        $this->generatePreview();
    }

    /**
     * Update QR colors from pickers with opacity
     */
    private function updateQRColorsFromPickers()
    {
        // Convert colors with opacity
        $bgOpacity = $this->qrBackgroundOpacity / 100;
        $borderOpacity = $this->qrBorderOpacity / 100;
        $darkOpacity = $this->qrDarkOpacity / 100;

        // Update styles dengan RGBA colors
        $this->styles['qrcode']['backgroundColor'] = $this->hexToRgba($this->qrBackgroundColorPicker, $bgOpacity);
        $this->styles['qrcode']['borderColor'] = $this->hexToRgba($this->qrBorderColorPicker, $borderOpacity);
        $this->styles['qrcode']['darkColor'] = $this->hexToRgba($this->qrDarkColorPicker, $darkOpacity);

        // Log untuk debugging
        logger('✅ QR Colors updated via Livewire:');
        logger('   Background: ' . $this->styles['qrcode']['backgroundColor']);
        logger('   Border: ' . $this->styles['qrcode']['borderColor']);
        logger('   Dark: ' . $this->styles['qrcode']['darkColor']);

        // Generate preview
        $this->generatePreview();
    }

    public function addLink()
    {
        $this->links[] = [
            'judul' => '',
            'url' => '',
            'icon' => '', // Icon - kosong berarti tidak ada icon
            'active' => true, // Active state - default true
            // PERBAIKAN: Toggle system untuk per-link styling
            'enableCustomStyling' => false, // DEFAULT: DISABLED - gunakan global style
            'backgroundColor' => '#FFFFFF', // Default white background (hanya digunakan jika enableCustomStyling = true)
            'textColor' => '#002366'          // Default blue text (hanya digunakan jika enableCustomStyling = true)
        ];
    }

    public function removeLink($index)
    {
        if (count($this->links) > 1) {
            unset($this->links[$index]);
            $this->links = array_values($this->links);
            $this->generatePreview();
        }
    }

    public function reorderLinks($orderedIndexes)
    {
        $reorderedLinks = [];
        foreach ($orderedIndexes as $index) {
            if (isset($this->links[$index])) {
                $reorderedLinks[] = $this->links[$index];
            }
        }
        $this->links = $reorderedLinks;
        $this->generatePreview();
    }

    public function generatePreview()
    {
        try {
            $this->previewHtml = $this->buildHtml(true);
        } catch (\Exception $e) {
            logger('Error generating preview: ' . $e->getMessage());
            $this->previewHtml = $this->buildPlaceholderHtml();
        }
    }

    public function updatePreview()
    {
        $this->generatePreview();
    }

    public function setQRColor($darkColor)
    {
        $this->styles['qrcode']['darkColor'] = $darkColor;
        $borderColor = $this->styles['qrcode']['borderColor'];
        // Background QR otomatis sama dengan border color
        $this->styles['qrcode']['backgroundColor'] = $borderColor;
        $this->styles['qrcode']['lightColor'] = $borderColor;
        $this->updatePreview();
    }

    public function generateFinalHtml()
    {
        // Gunakan final slug untuk validasi
        $originalSlug = $this->slug;
        $this->slug = $this->getFinalSlug();

        $this->validate();

        $validLinks = $this->getValidLinks();

        if (empty($validLinks)) {
            session()->flash('error', 'Minimal harus ada satu link yang valid.');
            return;
        }

        try {
            $this->generatedHtml = $this->buildHtml(false);
            $this->dispatch('htmlGenerated');
            session()->flash('success', 'HTML berhasil digenerate!');
        } catch (\Exception $e) {
            $this->dispatch('htmlError', message: $e->getMessage());
            session()->flash('error', 'Error generating HTML: ' . $e->getMessage());
        } finally {
            // Restore slug untuk UI
            $this->slug = $originalSlug;
        }
    }

    // UPDATE: Method saveToDatabase() untuk menggunakan final slug
    public function saveToDatabase()
    {
        $originalSlug = $this->slug;
        $this->slug = $this->getFinalSlug();

        $this->validate();

        if (!$this->validateSlug()) {
            $this->slug = $originalSlug;
            session()->flash('error', 'Slug tidak valid atau sudah di-reserve sistem.');
            return;
        }

        $validLinks = $this->getValidLinks();

        if (empty($validLinks)) {
            $this->dispatch('databaseError', message: 'Minimal harus ada satu link yang valid.');
            session()->flash('error', 'Minimal harus ada satu link yang valid.');
            $this->slug = $originalSlug;
            return;
        }

        $existingTautan = Tautan::where('slug', $this->slug)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingTautan) {
            $this->dispatch('databaseError', message: 'Slug "' . $this->slug . '" sudah digunakan.');
            session()->flash('error', 'Slug sudah digunakan. Gunakan slug yang berbeda.');
            $this->slug = $originalSlug;
            return;
        }

        try {
            // ✅ HANDLE LOGO UPLOAD - LIVEWIRE CORRECT WAY
            $logoPath = $this->logoUrl; // Default ke URL eksternal
            $judulGambar = null;

            if ($this->useUploadedLogo && $this->logoUpload) {
                try {
                    logger('🔍 === UPLOAD START ===');
                    logger('File name: ' . $this->logoUpload->getClientOriginalName());
                    logger('File size: ' . $this->logoUpload->getSize() . ' bytes');
                    logger('File mime: ' . $this->logoUpload->getMimeType());

                    // Validate file
                    $this->validate([
                        'logoUpload' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048'
                    ]);

                    // Generate filename
                    $extension = $this->logoUpload->getClientOriginalExtension();
                    $filename = $this->slug . '-logo.' . $extension;

                    logger('📝 Target filename: ' . $filename);
                    logger('📍 Target disk: public');
                    logger('📂 Target path: logos/' . $filename);

                    // ✅ CARA LIVEWIRE YANG BENAR - storeAs
                    $storedPath = $this->logoUpload->storeAs(
                        'logos',           // folder dalam public/storage/
                        $filename,         // nama file
                        'public'           // disk yang digunakan
                    );

                    logger('✅ storeAs() returned: ' . $storedPath);

                    // Verify file exists di filesystem
                    $fullPath = Storage::disk('public')->path($storedPath);
                    logger('🔍 Checking file at: ' . $fullPath);

                    if (Storage::disk('public')->exists($storedPath)) {
                        logger('✅ File exists! Size: ' . Storage::disk('public')->size($storedPath) . ' bytes');

                        // Path untuk database (URL accessible)
                        $logoPath = Storage::disk('public')->url($storedPath);
                        $judulGambar = $filename;

                        logger('✅ Final logo_url for DB: ' . $logoPath);
                        logger('✅ Storage::url() result: ' . $logoPath);
                    } else {
                        throw new \Exception('File tidak ditemukan di: ' . $fullPath);
                    }
                } catch (\Illuminate\Validation\ValidationException $e) {
                    logger('❌ Validation error: ' . json_encode($e->errors()));
                    session()->flash('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
                    $this->slug = $originalSlug;
                    return;
                } catch (\Exception $uploadError) {
                    logger('❌ Upload error: ' . $uploadError->getMessage());
                    logger('❌ Stack trace: ' . $uploadError->getTraceAsString());
                    session()->flash('error', 'Gagal upload gambar: ' . $uploadError->getMessage());
                    $this->slug = $originalSlug;
                    return;
                }
            }

            logger('💾 Saving to database with logo_url: ' . $logoPath);

            // Debug: Log QR colors being saved
            logger('🎨 QR Border Color being saved: ' . ($this->styles['qrcode']['borderColor'] ?? 'NOT SET'));
            logger('🎨 QR Dark Color being saved: ' . ($this->styles['qrcode']['darkColor'] ?? 'NOT SET'));
            logger('🎨 Full QR Styles being saved: ' . json_encode($this->styles['qrcode'] ?? []));

            // ✅ SIMPAN KE DATABASE - Simpan SEMUA links (termasuk yang inactive)
            // Filter hanya link yang punya judul dan url (tapi boleh inactive)
            $linksToSave = array_values(array_filter($this->links, function ($link) {
                return !empty($link['judul']) && !empty($link['url']);
            }));

            $tautan = Tautan::create([
                'user_id' => Auth::id(),
                'title' => $this->judul,
                'description' => $this->deskripsi,
                'slug' => $this->slug,
                'links' => $linksToSave,
                'styles' => $this->styles,
                'logo_url' => $logoPath,
                'judul_gambar' => $judulGambar,
                'use_uploaded_logo' => $this->useUploadedLogo,
                'tujuan' => 'internal',
                'footer_text_1' => $this->footerText1,
                'footer_text_2' => $this->footerText2,
                'is_active' => true,
            ]);

            // ✅ GENERATE PUBLIC URL
            $publicUrl = route('tautan.show', $tautan->slug);

            $this->dispatch('databaseSaved');
            session()->flash('success', 'Tautan berhasil disimpan!');
            session()->flash('public_url', $publicUrl);

            logger('✅ Tautan saved successfully: ' . $publicUrl);
            logger('📸 Logo URL: ' . $logoPath);

            // ✅ Redirect ke halaman kelola tautan setelah berhasil menyimpan
            return redirect()->route('kelola-tautan');
        } catch (\Exception $e) {
            $this->dispatch('databaseError', message: $e->getMessage());
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
            logger('❌ Error saving tautan: ' . $e->getMessage());
        } finally {
            $this->slug = $originalSlug;
        }
    }

    private function getValidLinks()
    {
        return array_filter($this->links, function ($link) {
            // Filter link yang: ada judul, ada url, dan active = true
            $isActive = $link['active'] ?? true; // Default true untuk backward compatibility
            return !empty($link['judul']) && !empty($link['url']) && $isActive;
        });
    }

    private function buildHtml($isPreview = false)
    {
        $validLinks = $this->getValidLinks();

        if ($isPreview && (empty($this->judul) || empty($validLinks))) {
            return $this->buildPlaceholderHtml();
        }

        $safeTitle = htmlspecialchars($this->judul ?: 'Judul Tautan', ENT_QUOTES, 'UTF-8');
        $safeDesc = htmlspecialchars($this->deskripsi ?: '', ENT_QUOTES, 'UTF-8');

        // Updated logo handling
        $safeLogo = $this->getCurrentLogoSource();
        $safeLogo = htmlspecialchars($safeLogo, ENT_QUOTES, 'UTF-8');

        $safeSlug = htmlspecialchars($this->slug ?: 'tautan', ENT_QUOTES, 'UTF-8');
        $safeFooter1 = htmlspecialchars($this->footerText1 ?: '', ENT_QUOTES, 'UTF-8');
        $safeFooter2 = htmlspecialchars($this->footerText2 ?: '', ENT_QUOTES, 'UTF-8');

        $css = $this->buildOptimizedCSS();

        $linkItems = '';
        foreach ($validLinks as $link) {
            $linkUrl = htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8');
            $linkJudul = htmlspecialchars($link['judul'], ENT_QUOTES, 'UTF-8');

            // Icon support
            $icon = $link['icon'] ?? '';
            $iconHtml = '';
            if (!empty($icon)) {
                $iconHtml = "<i class=\"{$icon} link-icon\"></i> ";
            }

              // PERBAIKAN FINAL: CONTEK EXACT HTML structure dari public.blade.php!
            // Conditional inline styling - HANYA untuk custom styling per-link (SAMA dengan public.blade.php)
            $enableCustomStyling = $link['enableCustomStyling'] ?? false;

            if ($enableCustomStyling) {
                // Custom styling ENABLED - gunakan warna dari link ini (EXACT method dari public.blade.php)
                $linkBgColor = htmlspecialchars($link['backgroundColor'] ?? $this->styles['button']['backgroundColor'], ENT_QUOTES, 'UTF-8');
                $linkTextColor = htmlspecialchars($link['textColor'] ?? $this->styles['button']['color'], ENT_QUOTES, 'UTF-8');
                // Build inline style dengan warna dari link ini (EXACT method dari public.blade.php)
                $inlineStyle = "background: {$linkBgColor}; color: {$linkTextColor};";
                $linkItems .= "                        <a href=\"{$linkUrl}\" class=\"link-button custom-styled\" style=\"{$inlineStyle}\" target=\"_blank\" rel=\"noopener noreferrer\">\n                            {$iconHtml}{$linkJudul}\n                        </a>\n";
            } else {
                // Custom styling DISABLED - gunakan global style dari styles['button'] (EXACT method dari public.blade.php)
                // JANGAN gunakan inline style, biarkan CSS global yang handle hover effects! (EXACT dari public.blade.php)
                $linkItems .= "                        <a href=\"{$linkUrl}\" class=\"link-button\" target=\"_blank\" rel=\"noopener noreferrer\">\n                            {$iconHtml}{$linkJudul}\n                        </a>\n";
            }
        }

        $footerContent = $this->buildFooterContent($safeFooter1, $safeFooter2);

        // KEMBALI KE FORMAT COMMENT SEDERHANA - SESUAI GITHUB PAGE CREATOR
        $imageIndicator = $this->useUploadedLogo ? 'true' : 'false';
        $filenameComment = $isPreview ? '' : "<!-- filename: {$safeSlug} -->\n<!-- image: {$imageIndicator} -->\n";

        return $this->buildCompleteHtml($filenameComment, $safeTitle, $safeDesc, $css, $safeLogo, $linkItems, $footerContent);
    }

    private function buildFooterContent($footer1, $footer2)
    {
        if (empty($footer1) && empty($footer2)) {
            return '';
        }

        $footerContent = "<div class=\"footer\">\n";
        if (!empty($footer1)) {
            $footerContent .= "            <p>{$footer1}</p>\n";
        }
        if (!empty($footer2)) {
            $footerContent .= "            <p>{$footer2}</p>\n";
        }
        $footerContent .= "        </div>";

        return $footerContent;
    }

    private function getOgImageUrl()
    {
        // Check if QR Code is enabled and has custom styling
        $qrSettings = $this->styles['qrcode'] ?? [];
        if ($qrSettings['enabled'] ?? false) {
            // Generate OG Image with QR Code styling
            return $this->generateQrCodeOgImage();
        }

        // Fallback to traditional logo-based OG Image
        if ($this->useUploadedLogo && !empty($this->logoPreviewUrl)) {
            // Untuk upload mode - prediksi URL final di GitHub Pages
            if ($this->logoUpload) {
                $extension = $this->logoUpload->getClientOriginalExtension();
                return "https://bps2172.github.io/TautPinang/images/{$this->slug}-logo.{$extension}";
            }
        }

        // Mode URL atau fallback
        return $this->logoUrl ?: 'https://bps2172.github.io/TautPinang/images/default-logo.png';
    }

    /**
     * Generate OG Image URL with QR Code styling
     */
    private function generateQrCodeOgImage()
    {
        $qrSettings = $this->styles['qrcode'] ?? [];

        // Extract QR colors
        $bgColor = $qrSettings['backgroundColor'] ?? '#ffffff';
        $darkColor = $qrSettings['darkColor'] ?? '#000000';

        // Convert RGBA to hex if needed
        $bgColorHex = $this->rgbaToHex($bgColor);
        $darkColorHex = $this->rgbaToHex($darkColor);

        // Remove # for API compatibility
        $bgColorApi = ltrim($bgColorHex, '#');
        $darkColorApi = ltrim($darkColorHex, '#');

        // Build QR Code URL for OG Image
        $qrSize = 400; // OG Image standard size
        $data = urlencode($this->judul ?? 'Taut Pinang');

        // Use API with custom colors
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$qrSize}x{$qrSize}&data={$data}&color={$darkColorApi}&bgcolor={$bgColorApi}&margin=2";

        return $qrUrl;
    }

    private function buildOgTags($title, $description, $slug, $imageUrl)
    {
        $url = "https://bps2172.github.io/TautPinang/{$slug}.html";
        $titleWithBranding = "{$title} | Taut Pinang";
        $safeTitle = htmlspecialchars($titleWithBranding, ENT_QUOTES, 'UTF-8');
        $safeDesc = htmlspecialchars($description ?: "Halaman tautan resmi {$title} - BPS Kota Tanjungpinang", ENT_QUOTES, 'UTF-8');
        $safeImageUrl = htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8');

        return "    <meta property=\"og:title\" content=\"{$safeTitle}\">
    <meta property=\"og:description\" content=\"{$safeDesc}\">
    <meta property=\"og:type\" content=\"website\">
    <meta property=\"og:url\" content=\"{$url}\">
    <meta property=\"og:image\" content=\"{$safeImageUrl}\">
    <meta property=\"og:image:width\" content=\"400\">
    <meta property=\"og:image:height\" content=\"400\">
    <meta property=\"og:site_name\" content=\"Taut Pinang - BPS Kota Tanjungpinang\">
    <meta property=\"og:locale\" content=\"id_ID\">
    <meta name=\"twitter:card\" content=\"summary\">
    <meta name=\"twitter:title\" content=\"{$safeTitle}\">
    <meta name=\"twitter:description\" content=\"{$safeDesc}\">
    <meta name=\"twitter:image\" content=\"{$safeImageUrl}\">
    <meta name=\"author\" content=\"BPS Kota Tanjungpinang\">
    <meta name=\"robots\" content=\"index, follow\">";
    }
    private function buildCompleteHtml($filenameComment, $title, $desc, $css, $logo, $linkItems, $footerContent)
    {
        $descriptionHtml = $desc ? "\n        <p class=\"description\">{$desc}</p>" : "";

        // Generate OG tags
        $ogImageUrl = $this->getOgImageUrl();
        $ogTags = $this->buildOgTags($title, $desc, $this->slug, $ogImageUrl);

        // QR Code JavaScript
        $qrCodeScript = $this->buildQRCodeScript();

        // Format title dengan branding Taut Pinang
        $pageTitle = "{$title} | Taut Pinang";

        // BARU: Ambil warna dari styles untuk tombol copy
        $container = $this->styles['container'];
        $title_style = $this->styles['title'];

        // Tentukan background color tombol berdasarkan tipe background container
        $buttonBgColor = '#3B82F6'; // default fallback
        if ($container['backgroundType'] === 'gradient') {
            $buttonBgColor = $container['backgroundGradientStart'] ?? '#3B82F6';
        } else {
            $buttonBgColor = $container['backgroundColor'] ?? '#3B82F6';
        }

        // Ambil warna icon dari title color
        $iconColor = $title_style['color'] ?? '#FFFFFF';

        // Escape colors untuk HTML
        $safeButtonBgColor = htmlspecialchars($buttonBgColor, ENT_QUOTES, 'UTF-8');
        $safeIconColor = htmlspecialchars($iconColor, ENT_QUOTES, 'UTF-8');

        return $filenameComment . "<!DOCTYPE html>
<html lang=\"id\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$pageTitle}</title>
{$ogTags}
    <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
    <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
    <link href=\"https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap\" rel=\"stylesheet\">
    <link rel=\"icon\" href=\"images/favicon.png\" type=\"image/png\">
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css\">
    <style>
{$css}
    </style>
</head>
<body>
    <!-- Copy Link Button -->
    <button id=\"copyLinkBtn\" onclick=\"copyCurrentUrl()\"
            style=\"position: fixed; top: 20px; right: 20px; z-index: 1000; background: {$safeButtonBgColor}; color: {$safeIconColor}; border: none; padding: 12px; border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.15); cursor: pointer; transition: all 0.3s ease; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;\">
        <svg width=\"20\" height=\"20\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\"></path>
        </svg>
    </button>

    <div class=\"container\">
        <div class=\"logo\">
            <img src=\"{$logo}\" alt=\"Logo BPS\" onerror=\"this.style.display='none'\">
        </div>
        <h1 class=\"title\">{$title}</h1>{$descriptionHtml}
        <div class=\"links\">
{$linkItems}        </div>
        {$footerContent}
    </div>
{$qrCodeScript}

    <!-- Copy Button JavaScript -->
    <script>
    function copyCurrentUrl() {
        const url = window.location.href;

        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                showCopySuccess();
            }).catch(() => {
                fallbackCopy(url);
            });
        } else {
            fallbackCopy(url);
        }
    }

    function fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showCopySuccess();
    }

    function showCopySuccess() {
        const btn = document.getElementById('copyLinkBtn');
        const originalContent = btn.innerHTML;
        const originalBg = btn.style.background;

        btn.innerHTML = '<svg width=\"20\" height=\"20\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path></svg>';
        btn.style.background = '#10B981';

        setTimeout(() => {
            btn.innerHTML = originalContent;
            btn.style.background = originalBg;
        }, 2000);
    }

    // Custom link styling and hover effects
    document.addEventListener('DOMContentLoaded', function() {
        // 🎯 PERBAIKAN: DISABLE applyCustomColors - CSS sudah menghandle semua styling!
        // JANGAN gunakan JavaScript inline styles karena akan override CSS hover effects
        // CSS sudah mencakup normal state dan hover state dengan benar

        // // Apply custom colors to links with data attributes - DISABLED
        // const customLinks = document.querySelectorAll('.link-custom');
        // customLinks.forEach(link => {
        //     const bgColor = link.getAttribute('data-bg-color');
        //     const textColor = link.getAttribute('data-text-color');
        //     if (bgColor) link.style.background = bgColor;
        //     if (textColor) link.style.color = textColor;
        // });

        console.log('🎯 CSS-only styling enabled (JavaScript custom colors disabled)');

        // Copy button hover effects
        const btn = document.getElementById('copyLinkBtn');
        const originalBg = '{$safeButtonBgColor}';

        // Generate darker version untuk hover (kurangi opacity atau darken)
        const hoverBg = adjustColorBrightness(originalBg, -20);

        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.background = hoverBg;
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.background = originalBg;
        });

        // Helper function untuk adjust brightness
        function adjustColorBrightness(color, amount) {
            // Jika rgba, extract RGB values
            const rgbaMatch = color.match(/rgba\\((\\d+),\\s*(\\d+),\\s*(\\d+),\\s*([\\d.]+)\\)/);
            if (rgbaMatch) {
                let r = Math.max(0, Math.min(255, parseInt(rgbaMatch[1]) + amount));
                let g = Math.max(0, Math.min(255, parseInt(rgbaMatch[2]) + amount));
                let b = Math.max(0, Math.min(255, parseInt(rgbaMatch[3]) + amount));
                let a = rgbaMatch[4];
                return `rgba(\${r}, \${g}, \${b}, \${a})`;
            }

            // Jika hex color
            const hexMatch = color.match(/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/);
            if (hexMatch) {
                let hex = hexMatch[1];
                if (hex.length === 3) {
                    hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
                }
                let r = Math.max(0, Math.min(255, parseInt(hex.substr(0, 2), 16) + amount));
                let g = Math.max(0, Math.min(255, parseInt(hex.substr(2, 2), 16) + amount));
                let b = Math.max(0, Math.min(255, parseInt(hex.substr(4, 2), 16) + amount));
                return `rgb(\${r}, \${g}, \${b})`;
            }

            // Fallback
            return color;
        }
    });
    </script>
</body>
</html>";
    }

    // ✅ BARU: Method untuk build comment yang informatif
    private function buildHtmlComment()
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $filename = $this->slug . '.html';
        $useUpload = $this->useUploadedLogo ? 'true' : 'false';
        $logoSource = $this->useUploadedLogo ? 'Upload Gambar' : 'URL Link';
        $tujuan = 'external'; // Default untuk form ini

        // Tentukan instruksi untuk gambar
        $imageInstructions = '';
        if ($this->useUploadedLogo && $this->logoUpload) {
            // Ambil ekstensi file asli
            $extension = $this->logoUpload->getClientOriginalExtension();
            $expectedImageName = $this->slug . '-logo.' . $extension;
            $imageInstructions = "\n<!-- PENTING: File ini menggunakan UPLOAD GAMBAR -->
<!-- Pastikan gambar logo sudah diupload ke folder 'images/' dengan nama: {$expectedImageName} -->
<!-- File gambar yang diperlukan: images/{$expectedImageName} -->";
        } else {
            $imageInstructions = "\n<!-- Info: File ini menggunakan URL gambar eksternal -->
<!-- Tidak perlu upload gambar tambahan ke GitHub -->";
        }

        return "<!--
=== TAUT PINANG - Generated HTML ===
Generated: {$timestamp}
Filename: {$filename}
Use Upload: {$useUpload}
Logo Source: {$logoSource}
Tujuan: {$tujuan}
Author: BPS Kota Tanjungpinang
-->{$imageInstructions}

";
    }

    private function buildQRCodeScript()
    {
        $qrSettings = $this->styles['qrcode'] ?? [];

        if (!($qrSettings['enabled'] ?? true)) {
            return '';
        }

        $position = $qrSettings['position'] ?? 'bottom-right';
        $size = $qrSettings['size'] ?? '100';
        $opacityValue = $qrSettings['opacity'] ?? '70';
        $opacity = $opacityValue / 100; // Convert 0-100 to 0.0-1.0
        $backgroundColor = $qrSettings['backgroundColor'] ?? '#FFFFFF';
        $borderColor = $qrSettings['borderColor'] ?? '#FFFFFF';
        $borderRadius = $qrSettings['borderRadius'] ?? '12';
        $padding = $qrSettings['padding'] ?? '10';
        $tooltipText = $qrSettings['tooltipText'] ?? 'Scan QR untuk buka halaman ini';

        // Handle both old and new QR settings structure for backward compatibility
        if (isset($qrSettings['darkColor']) && isset($qrSettings['backgroundColor'])) {
            // New structure - use colors directly (they already include transparency)
            $darkColor = $qrSettings['darkColor'] ?? '#000000';
            $lightColor = $qrSettings['backgroundColor'] ?? '#FFFFFF';
        } else {
            // Old structure - use opacity and individual colors
            $darkColor = $qrSettings['darkColor'] ?? '#002366';
            $borderColor = $qrSettings['borderColor'] ?? '#FFFFFF';
            // Convert hex to rgba with opacity
            $darkColor = $this->hexToRgba($darkColor, $opacity);
            $lightColor = $this->hexToRgba($borderColor, $opacity);
        }

        $showOnMobile = $qrSettings['showOnMobile'] ?? false;

        $positionStyles = $this->getQRPositionStyles($position);

        return "
<!-- QR Code Libraries with Multiple CDN Fallbacks -->
<script>
(function() {
    let qrLibLoaded = false;
    let currentCDN = 0;

    const cdnList = [
        'https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js',
        'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js'
    ];

    function loadQRLibrary() {
        if (currentCDN >= cdnList.length) {
            console.warn('❌ Semua CDN QR Code gagal, menggunakan fallback API');
            initQRCode();
            return;
        }

        const script = document.createElement('script');
        script.src = cdnList[currentCDN];
        script.onload = function() {
            console.log('✅ QR Code library loaded from:', cdnList[currentCDN]);
            qrLibLoaded = true;
            initQRCode();
        };
        script.onerror = function() {
            console.warn('⚠️ CDN gagal:', cdnList[currentCDN]);
            currentCDN++;
            loadQRLibrary();
        };
        document.head.appendChild(script);
    }

    loadQRLibrary();

    function initQRCode() {
        if (document.readyState !== 'complete') {
            window.addEventListener('load', generateQRCode);
        } else {
            generateQRCode();
        }
    }

    function generateQRCode() {
        try {
            const qrSettings = {
                enabled: true,
                position: '{$position}',
                size: {$size},
                opacity: {$opacityValue},
                backgroundColor: '{$backgroundColor}',
                borderColor: '{$borderColor}',
                borderRadius: '{$borderRadius}',
                padding: '{$padding}',
                tooltipText: '{$tooltipText}',
                darkColor: '{$darkColor}',
                lightColor: '{$lightColor}',
                showOnMobile: " . ($showOnMobile ? 'true' : 'false') . "
            };

            if (!qrSettings.enabled) return;

            const existingQR = document.getElementById('qr-code-container');
            if (existingQR) existingQR.remove();

            const currentUrl = window.location.href;

            const qrContainer = document.createElement('div');
            qrContainer.id = 'qr-code-container';
            qrContainer.style.cssText = `
                position: fixed;
                {$positionStyles}
                background: \${qrSettings.backgroundColor};
                padding: \${qrSettings.padding}px;
                border-radius: \${qrSettings.borderRadius}px;
                z-index: 1000;
                border: 2px solid \${qrSettings.borderColor};
                opacity: 1;
                cursor: pointer;
                user-select: none;
                display: flex;
                align-items: center;
                justify-content: center;
            `;

            if (!qrSettings.showOnMobile) {
                function toggleQRVisibility() {
                    qrContainer.style.display = window.innerWidth <= 768 ? 'none' : 'block';
                }
                toggleQRVisibility();
                window.addEventListener('resize', toggleQRVisibility);
            }

            // Check if colors are completely transparent (opacity 0) - don't generate QR Code
            const darkColorOpacity = getOpacityFromColor(qrSettings.darkColor);
            const lightColorOpacity = getOpacityFromColor(qrSettings.lightColor);

            if (darkColorOpacity === 0 || lightColorOpacity === 0) {
                console.log('🚫 QR Code colors are transparent, not generating QR Code');
                // Don't create any QR Code if colors are transparent
                document.body.appendChild(qrContainer);
                return;
            }

            // Add hover tooltip
            const tooltip = document.createElement('div');
            tooltip.style.cssText = `
                position: absolute;
                bottom: 110%;
                right: 0;
                background: #000000;
                color: white;
                padding: 6px 10px;
                border-radius: 6px;
                font-size: 12px;
                white-space: nowrap;
                opacity: 0;
                transform: translateY(10px);
                transition: all 0.3s ease;
                pointer-events: none;
                z-index: 1001;
            `;
            tooltip.textContent = qrSettings.tooltipText;
            qrContainer.appendChild(tooltip);

            // Add hover events for tooltip
            qrContainer.addEventListener('mouseenter', function() {
                tooltip.style.opacity = '1';
                tooltip.style.transform = 'translateY(-5px)';
            });

            qrContainer.addEventListener('mouseleave', function() {
                tooltip.style.opacity = '0';
                tooltip.style.transform = 'translateY(10px)';
            });

            if (typeof QRCode !== 'undefined' && qrLibLoaded) {
                console.log('📊 Generating QR with QRCode.js library');
                const canvas = document.createElement('canvas');

                QRCode.toCanvas(canvas, currentUrl, {
                    width: qrSettings.size,
                    height: qrSettings.size,
                    margin: 1,
                    color: {
                        dark: qrSettings.darkColor,
                        light: qrSettings.lightColor
                    },
                    errorCorrectionLevel: 'M'
                }, function (error) {
                    if (error) {
                        console.error('QR Canvas failed:', error);
                        createFallbackQR(qrContainer, qrSettings, currentUrl);
                    } else {
                        canvas.style.cssText = `display: block; width: \${qrSettings.size}px; height: \${qrSettings.size}px; border-radius: 4px;`;
                        qrContainer.appendChild(canvas);
                        document.body.appendChild(qrContainer);
                        console.log('✅ QR Code berhasil dibuat dengan library');
                    }
                });
            } else {
                console.log('📊 Generating QR with fallback API');
                createFallbackQR(qrContainer, qrSettings, currentUrl);
            }

        } catch (error) {
            console.error('❌ Error in generateQRCode:', error);
        }
    }

    // Helper function to extract opacity from color string
    function getOpacityFromColor(color) {
        if (color.startsWith('rgba')) {
            const match = color.match(/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/);
            return match ? parseFloat(match[4]) : 1;
        }
        return 1; // Default opacity for hex colors
    }

    // Helper function to convert rgba to hex
    function rgbaToHex(color) {
        if (color.startsWith('rgba')) {
            const match = color.match(/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/);
            if (match) {
                const r = parseInt(match[1]);
                const g = parseInt(match[2]);
                const b = parseInt(match[3]);
                return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
            }
        }
        // Return hex if already hex
        return color.startsWith('#') ? color : '#' + color;
    }

    function createFallbackQR(container, settings, url) {
        // Check if colors are completely transparent (opacity 0) - don't generate QR Code
        const darkColorOpacity = getOpacityFromColor(settings.darkColor);
        const lightColorOpacity = getOpacityFromColor(settings.lightColor);

        if (darkColorOpacity === 0 || lightColorOpacity === 0) {
            console.log('🚫 QR Code colors are transparent, not generating QR Code');
            // Don't create any QR Code if colors are transparent
            document.body.appendChild(container);
            return;
        }

        // Add hover tooltip
        const tooltip = document.createElement('div');
        tooltip.style.cssText = `
            position: absolute;
            bottom: 110%;
            right: 0;
            background: #000000;
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            pointer-events: none;
            z-index: 1001;
        `;
        tooltip.textContent = settings.tooltipText;
        container.appendChild(tooltip);

        // Add hover events for tooltip
        container.addEventListener('mouseenter', function() {
            tooltip.style.opacity = '1';
            tooltip.style.transform = 'translateY(-5px)';
        });

        container.addEventListener('mouseleave', function() {
            tooltip.style.opacity = '0';
            tooltip.style.transform = 'translateY(10px)';
        });

        const img = document.createElement('img');
        // Convert RGBA to hex for API compatibility
        const darkColorHex = rgbaToHex(settings.darkColor);
        const lightColorHex = rgbaToHex(settings.lightColor);

        // Debug: Log colors being used for QR generation
        console.log('🎨 QR Code Colors:');
        console.log('   Original darkColor:', settings.darkColor);
        console.log('   Original lightColor:', settings.lightColor);
        console.log('   Converted darkColorHex:', darkColorHex);
        console.log('   Converted lightColorHex:', lightColorHex);

        const apiList = [
            `https://api.qrserver.com/v1/create-qr-code/?size=\${settings.size}x\${settings.size}&data=\${encodeURIComponent(url)}&color=\${darkColorHex.replace('#', '')}&bgcolor=\${lightColorHex.replace('#', '')}&margin=1&t=\${Date.now()}`,
            `https://chart.googleapis.com/chart?chs=\${settings.size}x\${settings.size}&cht=qr&chl=\${encodeURIComponent(url)}&chof=png&t=\${Date.now()}`,
            `https://quickchart.io/qr?text=\${encodeURIComponent(url)}&size=\${settings.size}&dark=#\${darkColorHex}&light=#\${lightColorHex}&t=\${Date.now()}`
        ];

        let currentAPI = 0;

        function tryAPI() {
            if (currentAPI >= apiList.length) {
                console.error('❌ Semua API QR Code gagal');
                container.style.display = 'none';
                return;
            }

            const apiUrl = apiList[currentAPI];
            console.log(`🔄 Trying API \${currentAPI + 1}:`, apiUrl);

            img.src = apiUrl;
            img.alt = 'QR Code';
            img.style.cssText = `width: \${settings.size}px; height: \${settings.size}px; display: block; border-radius: 4px;`;

            img.onerror = function() {
                console.warn('⚠️ API QR failed:', apiUrl);
                currentAPI++;
                tryAPI();
            };

            img.onload = function() {
                console.log('✅ QR Code berhasil dengan API:', apiUrl);
                container.appendChild(img);
                document.body.appendChild(container);
            };
        }

        tryAPI();
    }

    })();
</script>";
    }

    private function getQRPositionStyles($position)
    {
        switch ($position) {
            case 'top-left':
                return 'top: 80px; left: 20px;';
            case 'top-right':
                return 'top: 80px; right: 20px;';
            case 'bottom-left':
                return 'bottom: 20px; left: 20px;';
            case 'bottom-right':
            default:
                return 'bottom: 20px; right: 20px;';
        }
    }

    private function buildPlaceholderHtml()
    {
        return "<!DOCTYPE html>
<html lang=\"id\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Preview | Taut Pinang</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
        }
        .placeholder {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: #6b7280;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .text {
            font-size: 16px;
            margin-bottom: 8px;
        }
        .subtext {
            font-size: 14px;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class=\"placeholder\">
        <div class=\"icon\">📱</div>
        <div class=\"text\">Live Preview | Taut Pinang</div>
        <div class=\"subtext\">Isi judul dan tambahkan link untuk melihat preview</div>
    </div>
</body>
</html>";
    }

    private function buildOptimizedCSS()
    {
        $bg = $this->styles['background'];
        $container = $this->styles['container'];
        $title = $this->styles['title'];
        $desc = $this->styles['description'];
        $button = $this->styles['button'];
        $buttonHover = $this->styles['buttonHover'];
        $logo = $this->styles['logo'];
        $footer = $this->styles['footer'];

        $backgroundGradient = $this->buildGradient(
            $bg['gradientStart'] ?? '#002366',
            $bg['gradientEnd'] ?? '#1D4E5F',
            $bg['direction'] ?? '135'
        );

        // PERBAIKAN: Samakan dengan public.blade.php method yang WORKING!
        $topGradient = "linear-gradient(90deg, " . ($container['topGradientStart'] ?? '#FFD700') . ", " . ($container['topGradientEnd'] ?? '#002366') . ")";

        // PERBAIKAN CONTROLLER: CONTEK INLINE CSS dari public.blade.php!
        // JANGAN generate CSS di PHP, gunakan inline variables di CSS template
        $hoverBgStartRaw = $buttonHover['backgroundStart'] ?? '#FFD700';
        $hoverBgEndRaw = $buttonHover['backgroundEnd'] ?? '#FFFFFF';
        $hoverBgStartOpacity = $buttonHover['backgroundStartOpacity'] ?? 100;
        $hoverBgEndOpacity = $buttonHover['backgroundEndOpacity'] ?? 100;

        // Process dengan opacity untuk inline CSS
        $hoverBgStartProcessed = $this->processColorWithOpacity($hoverBgStartRaw, $hoverBgStartOpacity);
        $hoverBgEndProcessed = $this->processColorWithOpacity($hoverBgEndRaw, $hoverBgEndOpacity);

        // Generate CSS string untuk inline injection (CONTEK DARI PUBLIC BLADE)
        $buttonHoverBg = "linear-gradient(135deg, {$hoverBgStartProcessed}, {$hoverBgEndProcessed})";

        // Debug: Log semua button colors dan CSS yang akan di-generate
        logger('=== BUTTON CSS DEBUG ===');
        logger('Button Background: ' . ($button['backgroundColor'] ?? 'NOT SET'));
        logger('Button Text Color: ' . ($button['color'] ?? 'NOT SET'));
        logger('Button Border: ' . ($button['borderWidth'] ?? 'NOT SET') . 'px ' . ($button['borderStyle'] ?? 'solid') . ' ' . ($button['borderColor'] ?? 'NOT SET'));
        logger('buttonHover backgroundStart: ' . ($buttonHover['backgroundStart'] ?? 'NOT SET'));
        logger('buttonHover backgroundEnd: ' . ($buttonHover['backgroundEnd'] ?? 'NOT SET'));
        logger('buttonHover backgroundStartOpacity: ' . ($buttonHover['backgroundStartOpacity'] ?? 'NOT SET'));
        logger('buttonHover backgroundEndOpacity: ' . ($buttonHover['backgroundEndOpacity'] ?? 'NOT SET'));
        logger('Generated buttonHoverBg: ' . $buttonHoverBg);
        logger('buttonHover color: ' . ($buttonHover['color'] ?? 'NOT SET'));
        logger('buttonHover border: ' . ($buttonHover['borderWidth'] ?? 'NOT SET') . 'px ' . ($buttonHover['borderStyle'] ?? 'solid') . ' ' . ($buttonHover['borderColor'] ?? 'NOT SET'));
        logger('buttonHover glow: ' . ($buttonHover['glowBlur'] ?? 'NOT SET') . 'px ' . ($buttonHover['glowColor'] ?? 'NOT SET'));

        $containerBackground = $this->buildBackgroundStyle($container);
        $logoBackground = $this->buildBackgroundStyle($logo);
        $logoBorderRadius = isset($logo['borderRadius']) ? $logo['borderRadius'] . 'px' : '50px';
        $bgAnimationCSS = ''; // No animations

        return $this->buildCSSString([
            'backgroundGradient' => $backgroundGradient,
            'topGradient' => $topGradient,
            'buttonHoverBg' => $buttonHoverBg,
            'containerBackground' => $containerBackground,
            'logoBackground' => $logoBackground,
            'logoBorderRadius' => $logoBorderRadius,
            'bgAnimationCSS' => $bgAnimationCSS,
            'container' => $container,
            'title' => $title,
            'desc' => $desc,
            'button' => $button,
            'buttonHover' => $buttonHover,
            'logo' => $logo,
            'footer' => $footer
        ]);
    }

    private function buildGradient($startColor, $endColor, $direction)
    {
        return "background: linear-gradient({$direction}deg, {$startColor}, {$endColor});";
    }

    private function buildBackgroundStyle($config)
    {
        $type = $config['backgroundType'] ?? 'solid';

        if ($type === 'gradient') {
            return $this->buildGradient(
                $config['backgroundGradientStart'] ?? '#FFFFFF',
                $config['backgroundGradientEnd'] ?? '#F8FAFC',
                $config['backgroundGradientDirection'] ?? '135'
            );
        } else {
            return isset($config['backgroundColor']) ? "background: {$config['backgroundColor']};" : 'background: #FFFFFF;';
        }
    }

    private function buildCSSString($params)
    {
        extract($params);

        return "        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            {$backgroundGradient}
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

{$bgAnimationCSS}

        .container {
            {$containerBackground}
            backdrop-filter: blur({$container['backdropBlur']}px);
            border-radius: {$container['borderRadius']}px;
            padding: 30px 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 350px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
            position: relative;
            overflow: hidden;
            z-index: 10;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: {$container['topGradientHeight']}px;
            background: {$topGradient}
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 20px auto 20px;
            border-radius: {$logoBorderRadius};
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            {$logoBackground}
            display: flex;
            align-items: center;
            justify-content: center;
            border: {$logo['borderWidth']}px {$logo['borderStyle']} {$logo['borderColor']};
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .title {
            font-size: {$title['fontSize']}px;
            font-weight: {$title['fontWeight']};
            color: {$title['color']};
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .description {
            color: {$desc['color']};
            font-size: {$desc['fontSize']}px;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .link-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 20px;
            text-decoration: none;
            border-radius: {$button['borderRadius']}px;
            font-weight: 600;
            font-size: 15px;
            border: {$button['borderWidth']}px {$button['borderStyle']} {$button['borderColor']};
            background: {$button['backgroundColor']};
            color: {$button['color']};
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .link-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .link-button:hover:not(.custom-styled) {
            /* DEBUG: Hover Background - {$buttonHoverBg} */
            /* DEBUG: Hover Text - {$buttonHover['color']} */
            /* DEBUG: Hover Border - {$buttonHover['borderWidth']}px {$buttonHover['borderStyle']} {$buttonHover['borderColor']} */
            transform: translateY(-2px) !important;
            background: {$buttonHoverBg} !important;
            color: {$buttonHover['color']} !important;
            border: {$buttonHover['borderWidth']}px {$buttonHover['borderStyle']} {$buttonHover['borderColor']} !important;
            box-shadow: 0 0 {$buttonHover['glowBlur']}px {$buttonHover['glowColor']} !important;
        }

        /* Custom styled links - PARSIAL hover: transform + glow effects ONLY */
        .link-button.custom-styled:hover {
            transform: translateY(-2px) !important;
            /* Keep original custom background and text colors */
            border: {$buttonHover['borderWidth']}px {$buttonHover['borderStyle']} {$buttonHover['borderColor']} !important;
            box-shadow: 0 0 {$buttonHover['glowBlur']}px {$buttonHover['glowColor']} !important;
        }

        /* Custom styled links - use data attributes for individual styling */
        .link-custom {
            /* Custom background and text colors will be set via JavaScript from data attributes */
        }

        .footer {
            margin-top: {$footer['marginTop']}px;
            padding-top: {$footer['paddingTop']}px;
            border-top: 1px solid {$footer['borderColor']};
            color: {$footer['color']};
            font-size: {$footer['fontSize']}px;
        }


        @media (max-width: 768px) {
            #qr-code-container {
                display: none !important;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px 20px;
            }

            .title {
                font-size: calc({$title['fontSize']}px * 0.85);
            }

            .link-button {
                padding: 14px 18px;
                font-size: 14px;
            }
        }";
    }

    private function generateOptimizedBackgroundAnimationCSS($bgAnim)
    {
        if (empty($bgAnim['type']) || $bgAnim['type'] === 'none') {
            return '';
        }

        $speed = $bgAnim['speed'] ?? '3';
        $intensity = ($bgAnim['intensity'] ?? 50) / 100;
        $color1 = $bgAnim['color1'] ?? '#002366';
        $color2 = $bgAnim['color2'] ?? '#FFD700';
        $color3 = $bgAnim['color3'] ?? '#1D4E5F';
        $direction = $bgAnim['direction'] ?? '0';
        $size = $bgAnim['size'] ?? '40';

        switch ($bgAnim['type']) {
            case 'gradient':
                return $this->buildGradientAnimation($color1, $color2, $color3, $direction, $speed, $intensity);
            case 'particles':
                return $this->buildParticleAnimation($color1, $color2, $color3, $size, $speed, $intensity);
            case 'waves':
                return $this->buildWaveAnimation($color1, $color2, $color3, $direction, $speed, $intensity);
            default:
                return '';
        }
    }

    private function buildGradientAnimation($color1, $color2, $color3, $direction, $speed, $intensity)
    {
        // OPTIMIZED: Hapus scale & rotate, kurangi blur, tambahkan GPU acceleration
        return "
        body::before,
        body::after {
            content: '';
            position: fixed;
            top: -10%;
            left: -10%;
            width: 120%;
            height: 120%;
            z-index: 2;
            pointer-events: none;
            will-change: background-position;
            transform: translateZ(0);
        }

        body::before {
            background: linear-gradient(
                {$direction}deg,
                {$color1} 0%,
                {$color2} 35%,
                {$color3} 70%,
                {$color1} 100%
            );
            background-size: 300% 300%;
            opacity: " . ($intensity * 0.4) . ";
            animation: gradientFlow1 {$speed}s ease-in-out infinite;
            filter: blur(15px);
        }

        body::after {
            background: linear-gradient(
                " . ($direction + 90) . "deg,
                {$color2} 0%,
                {$color3} 50%,
                {$color1} 100%
            );
            background-size: 300% 300%;
            opacity: " . ($intensity * 0.3) . ";
            animation: gradientFlow2 " . ($speed * 1.5) . "s ease-in-out infinite reverse;
            animation-delay: " . ($speed * 0.2) . "s;
            filter: blur(12px);
        }

        @keyframes gradientFlow1 {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        @keyframes gradientFlow2 {
            0%, 100% {
                background-position: 0% 0%;
            }
            50% {
                background-position: 100% 100%;
            }
        }";
    }

    private function buildParticleAnimation($color1, $color2, $color3, $size, $speed, $intensity)
    {
        // OPTIMIZED: Kurangi particles dari 10 jadi 6, hapus scale, kurangi blur
        $baseSize = $size * 0.8; // Sedikit lebih kecil untuk performa

        return "
        body::before,
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
            will-change: transform;
            transform: translateZ(0);
        }

        body::before {
            background:
                radial-gradient(circle {$baseSize}px at 20% 30%, {$color1} 0%, transparent 70%),
                radial-gradient(circle " . ($baseSize * 0.9) . "px at 80% 20%, {$color2} 0%, transparent 70%),
                radial-gradient(circle " . ($baseSize * 1.1) . "px at 70% 80%, {$color3} 0%, transparent 70%);
            opacity: " . ($intensity * 0.35) . ";
            animation: particleFloat1 {$speed}s ease-in-out infinite;
            filter: blur(1px);
        }

        body::after {
            background:
                radial-gradient(circle " . ($baseSize * 0.95) . "px at 50% 60%, {$color3} 0%, transparent 70%),
                radial-gradient(circle " . ($baseSize * 1.05) . "px at 10% 50%, {$color1} 0%, transparent 70%),
                radial-gradient(circle {$baseSize}px at 90% 40%, {$color2} 0%, transparent 70%);
            opacity: " . ($intensity * 0.3) . ";
            animation: particleFloat2 " . ($speed * 1.3) . "s ease-in-out infinite;
            animation-delay: " . ($speed * 0.3) . "s;
            filter: blur(1.5px);
        }

        @keyframes particleFloat1 {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(15px, -20px, 0);
            }
        }

        @keyframes particleFloat2 {
            0%, 100% {
                transform: translate3d(0, 0, 0);
            }
            50% {
                transform: translate3d(-12px, 18px, 0);
            }
        }";
    }

    private function buildWaveAnimation($color1, $color2, $color3, $direction, $speed, $intensity)
    {
        return "
        body::before,
        body::after {
            content: '';
            position: fixed;
            top: -30%;
            left: -30%;
            width: 160%;
            height: 160%;
            z-index: 2;
            pointer-events: none;
            border-radius: 40%;
        }

        body::before {
            background:
                radial-gradient(ellipse 70% 50%, {$color1} 0%, {$color2} 40%, transparent 80%),
                radial-gradient(ellipse 50% 70%, {$color3} 20%, {$color1} 60%, transparent 90%);
            opacity: {$intensity};
            animation: waveFlow1 {$speed}s ease-in-out infinite;
            filter: blur(40px);
        }

        body::after {
            background:
                radial-gradient(ellipse 60% 80%, {$color2} 10%, {$color3} 50%, transparent 85%),
                radial-gradient(ellipse 80% 60%, {$color1} 30%, {$color2} 70%, transparent 95%);
            opacity: " . ($intensity * 0.8) . ";
            animation: waveFlow2 " . ($speed * 1.8) . "s ease-in-out infinite;
            animation-delay: " . ($speed * 0.2) . "s;
            filter: blur(50px);
        }

        @keyframes waveFlow1 {
            0%, 100% {
                transform: rotate({$direction}deg) scale(1) translateX(0%);
            }
            25% {
                transform: rotate({$direction}deg) scale(1.2) translateX(5%);
            }
            50% {
                transform: rotate({$direction}deg) scale(1.4) translateX(15%);
            }
            75% {
                transform: rotate({$direction}deg) scale(1.1) translateX(8%);
            }
        }

        @keyframes waveFlow2 {
            0%, 100% {
                transform: rotate(" . ($direction + 120) . "deg) scale(1.3) translateY(0%);
            }
            33% {
                transform: rotate(" . ($direction + 120) . "deg) scale(0.9) translateY(-10%);
            }
            66% {
                transform: rotate(" . ($direction + 120) . "deg) scale(1.1) translateY(-20%);
            }
        }";
    }

    public function downloadHtml()
    {
        if (empty($this->generatedHtml)) {
            $this->generateFinalHtml();
        }

        if (empty($this->generatedHtml)) {
            session()->flash('error', 'Tidak dapat mengunduh HTML kosong.');
            return;
        }

        $fileName = ($this->slug ?: 'tautan') . '.html';

        return response()->streamDownload(function () {
            echo $this->generatedHtml;
        }, $fileName, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    public function resetForm()
    {
        // Clean up any temporary files
        if (!empty($this->uploadedLogoPath) && Storage::disk('public')->exists($this->uploadedLogoPath)) {
            Storage::disk('public')->delete($this->uploadedLogoPath);
        }

        // Reset form fields
        $this->reset([
            'judul',
            'deskripsi',
            'slug',
            'links',
            'footerText1',
            'footerText2',
            'generatedHtml',
            'previewHtml',
            'logoUpload',
            'logoPreviewUrl',
            'uploadedLogoPath'
        ]);

        // PERBAIKAN: Explicitly reset logo-related flags
        $this->hasUploadedImage = false;
        $this->useUploadedLogo = false;  // DEFAULT ke URL mode

        $this->resetStylesToDefault();
        $this->logoUrl = 'https://i.pinimg.com/474x/e4/8a/b2/e48ab2e2e8055f15f04caf6968c1314a.jpg';
        $this->footerText1 = '© 2025 Made with ❤ BPS Kota Tanjungpinang -';
        $this->footerText2 = 'BPS Provinsi Kepulauan Riau';

        $this->addLink();
        $this->generatePreview();

        session()->flash('success', 'Form berhasil direset!');
    }

    public function getGithubUrlProperty()
    {
        if (empty($this->slug)) {
            return 'https://bps2172.github.io/TautPinang/preview.html';
        }
        return 'https://bps2172.github.io/TautPinang/' . $this->slug . '.html';
    }

    public function render()
    {
        return view('livewire.buat-tautan');
    }
    /**
     * ✅ VALIDASI SLUG - Cek Reserved Slugs
     */
    protected function validateSlug()
    {
        $reservedSlugs = [
            'dashboard',
            'login',
            'register',
            'logout',
            'password',
            'tautan',
            'admin',
            'api',
            'home',
            'about',
            'contact',
            'privacy',
            'terms',
            'user',
            'profile',
            'settings'
        ];

        if (in_array(strtolower($this->slug), $reservedSlugs)) {
            $this->addError('slug', 'Slug "' . $this->slug . '" tidak dapat digunakan.');
            return false;
        }

        return true;
    }

    /**
     * Convert hex color to RGBA with opacity
     */
    private function hexToRgba($hex, $opacity)
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Parse hex to RGB
        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return "rgba($r, $g, $b, $opacity)";
    }

    // Icon Picker Methods
    public function selectIcon($index)
    {
        $this->selectedLinkIndex = $index;
        $this->showIconPicker = true;
    }

    public function setIcon($iconClass)
    {
        if ($this->selectedLinkIndex !== null) {
            $this->links[$this->selectedLinkIndex]['icon'] = $iconClass;
            $this->showIconPicker = false;
            $this->selectedLinkIndex = null;
            $this->generatePreview();
        }
    }

    public function removeIcon()
    {
        if ($this->selectedLinkIndex !== null) {
            $this->links[$this->selectedLinkIndex]['icon'] = '';
            $this->showIconPicker = false;
            $this->selectedLinkIndex = null;
            $this->generatePreview();
        }
    }

    public function closeIconPicker()
    {
        $this->showIconPicker = false;
        $this->selectedLinkIndex = null;
    }
}
