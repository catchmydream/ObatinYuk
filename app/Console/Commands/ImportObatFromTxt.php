<?php

namespace App\Console\Commands;

use App\Models\Obat;
use Illuminate\Console\Command;

class ImportObatFromTxt extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'obat:import-txt {file : Path to the TXT file} {--clear : Clear all existing obat data before importing} {--update : Update existing obat by name instead of skipping}';

    /**
     * The console command description.
     */
    protected $description = 'Import obat data from a TXT file into the database';

    /**
     * Valid values for enum fields.
     */
    private array $validCategories = ['obat', 'vitamin', 'herbal'];
    private array $validAturanPakai = ['sebelum_makan', 'sesudah_makan', 'bersamaan_makan'];
    private array $validClassifications = ['bebas', 'terbatas', 'keras'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');

        // Resolve relative paths
        if (!file_exists($filePath)) {
            $filePath = base_path($filePath);
        }

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return self::FAILURE;
        }

        $content = file_get_contents($filePath);
        
        if (empty(trim($content))) {
            $this->error("File kosong!");
            return self::FAILURE;
        }

        // Parse the file
        $obatEntries = $this->parseFile($content);

        if (empty($obatEntries)) {
            $this->error("Tidak ada data obat yang valid ditemukan dalam file.");
            return self::FAILURE;
        }

        $this->info("Ditemukan " . count($obatEntries) . " obat dalam file.");
        $this->newLine();

        // Show preview table
        $previewData = collect($obatEntries)->map(function ($entry, $index) {
            return [
                '#' => $index + 1,
                'Nama' => \Illuminate\Support\Str::limit($entry['name'], 35),
                'Kategori' => $entry['category'],
                'Klasifikasi' => $entry['classification'],
                'Harga' => 'Rp ' . number_format($entry['price'], 0, ',', '.'),
                'Stok' => $entry['stock'],
            ];
        })->toArray();

        $this->table(['#', 'Nama', 'Kategori', 'Klasifikasi', 'Harga', 'Stok'], $previewData);
        $this->newLine();

        // Handle --clear option
        if ($this->option('clear')) {
            $existingCount = Obat::count();
            if ($existingCount > 0) {
                if (!$this->confirm("⚠️  PERINGATAN: Opsi --clear akan menghapus {$existingCount} obat yang sudah ada. Lanjutkan?")) {
                    $this->info("Import dibatalkan.");
                    return self::SUCCESS;
                }
                Obat::truncate();
                $this->warn("✓ {$existingCount} obat lama telah dihapus.");
            }
        }

        if (!$this->confirm("Lanjutkan import " . count($obatEntries) . " obat?")) {
            $this->info("Import dibatalkan.");
            return self::SUCCESS;
        }

        // Import
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $bar = $this->output->createProgressBar(count($obatEntries));
        $bar->start();

        foreach ($obatEntries as $index => $entry) {
            try {
                $existing = Obat::where('name', $entry['name'])->first();

                if ($existing) {
                    if ($this->option('update')) {
                        // Update existing, but preserve image
                        $updateData = collect($entry)->except(['image'])->toArray();
                        $existing->update($updateData);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    Obat::create($entry);
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'line' => $index + 1,
                    'name' => $entry['name'] ?? 'Unknown',
                    'error' => $e->getMessage(),
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("═══════════════════════════════════");
        $this->info("  HASIL IMPORT");
        $this->info("═══════════════════════════════════");
        $this->info("  ✅ Berhasil ditambahkan : {$imported}");
        
        if ($updated > 0) {
            $this->info("  🔄 Berhasil diupdate    : {$updated}");
        }
        
        if ($skipped > 0) {
            $this->warn("  ⏭️  Dilewati (sudah ada) : {$skipped}");
        }

        if (!empty($errors)) {
            $this->error("  ❌ Gagal                : " . count($errors));
            $this->newLine();
            $this->error("Detail error:");
            foreach ($errors as $err) {
                $this->error("  Obat #{$err['line']} ({$err['name']}): {$err['error']}");
            }
        }

        $this->newLine();
        $this->info("Total obat di database sekarang: " . Obat::count());
        $this->info("📷 Jangan lupa upload gambar via Filament admin panel!");

        return self::SUCCESS;
    }

    /**
     * Parse the TXT file content into an array of obat entries.
     */
    private function parseFile(string $content): array
    {
        // Remove comment lines (lines starting with #)
        $lines = explode("\n", $content);
        $cleanedLines = array_filter($lines, function ($line) {
            return !str_starts_with(trim($line), '#');
        });
        $content = implode("\n", $cleanedLines);

        // Split by "===" separator
        $blocks = preg_split('/^===\s*$/m', $content);
        
        $entries = [];

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) {
                continue;
            }

            $entry = $this->parseBlock($block);
            
            if ($entry !== null) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * Parse a single obat block into an associative array.
     */
    private function parseBlock(string $block): ?array
    {
        $lines = explode("\n", $block);
        $data = [];
        $currentKey = null;
        $currentValue = '';

        foreach ($lines as $line) {
            // Skip empty lines and comment lines
            if (empty(trim($line)) || str_starts_with(trim($line), '#')) {
                // If we're in a multi-line value, add a newline
                if ($currentKey !== null) {
                    $currentValue .= "\n";
                }
                continue;
            }

            // Check if this line is a key: value pair
            if (preg_match('/^([a-z_]+)\s*:\s*(.*)$/i', $line, $matches)) {
                // Save previous key-value pair
                if ($currentKey !== null) {
                    $data[$currentKey] = trim($currentValue);
                }

                $currentKey = strtolower(trim($matches[1]));
                $currentValue = trim($matches[2]);
            } elseif ($currentKey !== null) {
                // Multi-line continuation
                $currentValue .= "\n" . $line;
            }
        }

        // Save last key-value pair
        if ($currentKey !== null) {
            $data[$currentKey] = trim($currentValue);
        }

        // Validate required field
        if (empty($data['name'] ?? '')) {
            return null;
        }

        // Build and validate entry
        $entry = [
            'name' => $data['name'],
            'category' => $this->validateEnum($data['category'] ?? 'obat', $this->validCategories, 'obat'),
            'description' => $data['description'] ?? null,
            'dosage' => $data['dosage'] ?? null,
            'aturan_pakai' => $this->validateEnum($data['aturan_pakai'] ?? '', $this->validAturanPakai, null),
            'classification' => $this->validateEnum($data['classification'] ?? 'bebas', $this->validClassifications, 'bebas'),
            'price' => $this->parsePrice($data['price'] ?? '0'),
            'stock' => (int) ($data['stock'] ?? 0),
        ];

        // Show warning for invalid enum values
        if (isset($data['category']) && !in_array($data['category'], $this->validCategories)) {
            $this->warn("  ⚠️  '{$data['name']}': category '{$data['category']}' tidak valid, menggunakan 'obat'");
        }
        if (isset($data['classification']) && !in_array($data['classification'], $this->validClassifications)) {
            $this->warn("  ⚠️  '{$data['name']}': classification '{$data['classification']}' tidak valid, menggunakan 'bebas'");
        }

        return $entry;
    }

    /**
     * Validate an enum value, return default if invalid.
     */
    private function validateEnum(string $value, array $valid, ?string $default): ?string
    {
        $value = strtolower(trim($value));
        return in_array($value, $valid) ? $value : $default;
    }

    /**
     * Parse price string to float (handle various formats).
     */
    private function parsePrice(string $price): float
    {
        // Remove non-numeric chars except dots and commas
        $price = preg_replace('/[^0-9.,]/', '', $price);
        // Handle Indonesian number format (dot as thousand separator)
        $price = str_replace('.', '', $price);
        // Handle comma as decimal separator
        $price = str_replace(',', '.', $price);

        return (float) $price;
    }
}
