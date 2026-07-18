<?php

namespace App\Livewire;

use App\Models\Obat;
use App\Models\Gejala;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;


class Chatbot extends Component
{
    public $messages = [];
    public $inputMessage = '';
    public $isLoading = false;

    public function mount()
    {
        $this->messages[] = [
            'role' => 'bot',
            'content' => 'Halo! Saya adalah Asisten Apoteker AI. Ceritakan keluhan atau gejala penyakit Anda, dan saya akan merekomendasikan obat yang tepat dari apotek kami.',
            'time' => now()->format('H:i')
        ];
    }

    public function sendMessage($message = null)
    {
        $userMessage = $message ?? trim($this->inputMessage);
        
        if ($userMessage === '') {
            return;
        }

        $this->inputMessage = '';
        $this->isLoading = true;

        $this->messages[] = [
            'role'    => 'user',
            'content' => $userMessage,
            'time'    => now()->format('H:i'),
        ];

        // Langsung panggil getBotResponse (dispatch internal tidak bekerja di Livewire 3)
        $this->getBotResponse($userMessage);
    }

    #[On('fetch-bot-response')]
    public function getBotResponse($message)
    {
        // --- PROSES FORWARD CHAINING (HYBRID) ---
        
        // Helper function to normalize text
        $normalize = function ($text) {
            $text = str_replace('-', ' ', $text); // Ganti tanda hubung dengan spasi
            $text = preg_replace('/[^\w\s]/u', '', $text); // Hapus tanda baca
            $text = preg_replace('/\s+/', ' ', $text); // Satukan spasi
            return strtolower(trim($text));
        };

        $normalizedMessage = $normalize($message);

        // 1. Ambil semua gejala dari database
        $allGejalas = Gejala::all();
        $detectedGejalaIds = [];
        $detectedGejalaNames = [];
        $isAskingForVitamin = (stripos($message, 'vitamin') !== false || stripos($message, 'suplemen') !== false);
        $isAskingForHerbal = (stripos($message, 'herbal') !== false || stripos($message, 'jamu') !== false);

        // Pemetaan Sinonim Gejala
        $synonymMapping = [
            'meredakan pilek' => ['pilek', 'flu', 'bersin', 'ingusan', 'meler'],
            'hidung tersumbat' => ['hidung tersumbat', 'hidung mampet', 'hidung buntu', 'susah napas'],
            'batuk berdahak' => ['batuk berdahak', 'batuk lendir', 'batuk basah'],
            'demam' => ['demam', 'panas', 'meriang', 'fever'],
            'batuk kering' => ['batuk kering', 'dry cough'],
        ];

        // 2. Deteksi gejala dalam pesan user (Fakta)
        foreach ($allGejalas as $gejala) {
            $normalizedGejala = $normalize($gejala->name);
            $isMatched = false;

            if (stripos($normalizedMessage, $normalizedGejala) !== false) {
                $isMatched = true;
            } else {
                $standardName = strtolower(trim($gejala->name));
                if (isset($synonymMapping[$standardName])) {
                    foreach ($synonymMapping[$standardName] as $synonym) {
                        $normalizedSynonym = $normalize($synonym);
                        if (stripos($normalizedMessage, $normalizedSynonym) !== false) {
                            $isMatched = true;
                            break;
                        }
                    }
                }
            }

            if ($isMatched) {
                $detectedGejalaIds[] = $gejala->id;
                $detectedGejalaNames[] = $gejala->name;
            }
        }

        // 3. Inference Engine: Cari obat yang cocok dan hitung skor
        $recommendations = [];
        
        // Cari berdasarkan Gejala
        if (!empty($detectedGejalaIds)) {
            $obats = Obat::with('gejalas')->get();
            
            foreach ($obats as $obat) {
                $obatGejalaIds = $obat->gejalas->pluck('id')->toArray();
                $matchedIds = array_intersect($detectedGejalaIds, $obatGejalaIds);
                $matchCount = count($matchedIds);
                
                if ($matchCount > 0) {
                    // Hitung Skor: (Jumlah gejala cocok / total gejala yang terdeteksi pada user) * 100
                    $score = ($matchCount / count($detectedGejalaIds)) * 100;
                    
                    $recommendations[] = [
                        'id' => $obat->id,
                        'name' => $obat->name,
                        'category' => $obat->category,
                        'classification' => $obat->classification ?? 'Bebas',
                        'aturan_pakai' => $obat->aturan_pakai,
                        'description' => $obat->description,
                        'score' => round($score, 2),
                        'matched_gejala' => $obat->gejalas->whereIn('id', $matchedIds)->pluck('name')->toArray(),
                        'total_match' => $matchCount
                    ];
                }
            }
        }

        // Cari berdasarkan Kategori (Jika user minta vitamin/suplemen/herbal)
        if ($isAskingForVitamin || $isAskingForHerbal) {
            $categories = [];
            if ($isAskingForVitamin) $categories = array_merge($categories, ['vitamin', 'suplemen']);
            if ($isAskingForHerbal) $categories[] = 'herbal';

            // Query 1: Berdasarkan Kategori
            $vitsByCategory = Obat::whereIn('category', $categories)->get();
            
            // Query 2: Backup - Berdasarkan Nama (jika kategori belum diupdate)
            $vitsByName = collect();
            if ($isAskingForVitamin) {
                $vitsByName = Obat::where('name', 'like', '%vitamin%')
                                ->orWhere('name', 'like', '%vit %')
                                ->orWhere('name', 'like', '%enervon%') // Contoh populer
                                ->get();
            }

            $vits = $vitsByCategory->merge($vitsByName)->unique('id')->take(3);

            foreach ($vits as $vit) {
                // Jika belum ada di rekomendasi (dari gejala), tambahkan dengan skor statis tinggi
                if (!collect($recommendations)->contains('id', $vit->id)) {
                    $recommendations[] = [
                        'id' => $vit->id,
                        'name' => $vit->name,
                        'category' => $vit->category,
                        'classification' => $vit->classification ?? 'Bebas',
                        'aturan_pakai' => $vit->aturan_pakai,
                        'description' => $vit->description,
                        'score' => 100,
                        'matched_gejala' => [],
                        'total_match' => 0
                    ];
                }
            }
        }

        // 3.5 Fallback: Cari berdasarkan kemiripan Nama Produk (jika belum ada rekomendasi kuat)
        if (count($recommendations) < 3) {
            $allObatNames = Obat::pluck('name', 'id');
            foreach ($allObatNames as $id => $name) {
                // Jika nama produk disebut dalam pesan (minimal 4 karakter untuk menghindari typo/kata umum)
                if (strlen($name) > 3 && stripos($message, $name) !== false) {
                    if (!collect($recommendations)->contains('id', $id)) {
                        $obat = Obat::find($id);
                        $recommendations[] = [
                            'id' => $obat->id,
                            'name' => $obat->name,
                            'category' => $obat->category,
                            'classification' => $obat->classification ?? 'Bebas',
                            'aturan_pakai' => $obat->aturan_pakai,
                            'description' => $obat->description,
                            'score' => 100, // Skor tinggi karena nama disebut spesifik
                            'matched_gejala' => [],
                            'total_match' => 0
                        ];
                    }
                }
            }
        }

        // Urutkan berdasarkan skor tertinggi (Forward Chaining Ranking)
        usort($recommendations, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Ambil maksimal 3 rekomendasi terbaik
        $recommendations = array_slice($recommendations, 0, 3);

        // 4. Siapkan Konteks untuk AI (Generative)
        $contextData = "HASIL ANALISIS SISTEM:\n";
        if (empty($recommendations)) {
            $contextData .= "- Tidak ditemukan kecocokan produk (obat/vitamin/herbal) di database kami.\n";
        } else {
            foreach ($recommendations as $index => $rec) {
                $rank = $index + 1;
                $typeStr = $rec['category'] === 'herbal' ? 'Obat Herbal' : ucfirst($rec['category'] ?? 'produk');
                $contextData .= "- Peringkat {$rank}: {$rec['name']} (Skor Kecocokan: {$rec['score']}%)\n";
                if (!empty($rec['matched_gejala'])) {
                    $contextData .= "  Alasan: Cocok dengan gejala [" . implode(', ', $rec['matched_gejala']) . "]\n";
                } elseif (($isAskingForVitamin || $isAskingForHerbal) && in_array($rec['category'], ['vitamin', 'suplemen', 'herbal'])) {
                    $contextData .= "  Alasan: Rekomendasi {$rec['category']} sesuai permintaan Anda.\n";
                }
                $contextData .= "  Golongan/Klasifikasi: " . strtoupper($rec['classification'] ?? 'Bebas') . "\n";
                $contextData .= "  Aturan Pakai: " . ($rec['aturan_pakai'] ? str_replace('_', ' ', $rec['aturan_pakai']) : 'Sesuai petunjuk pada kemasan') . "\n";
                $contextData .= "  Deskripsi: {$rec['description']}\n\n";
            }
        }

        $systemPrompt = "Anda adalah seorang Apoteker AI yang ringkas, tegas, namun tetap ramah.
Berikut adalah hasil rekomendasi dari Sistem Pakar Forward Chaining:

{$contextData}

Tugas Anda:
1. Jelaskan hasil rekomendasi obat tersebut kepada pasien sesuai urutan peringkat yang diberikan di atas. Jangan mengubah urutan, skor, atau mengganti rekomendasi utama.
2. Sebutkan nama obat, persentase 'Skor Kecocokan', dan aturan pakainya secara jelas.
3. Jika obat tergolong 'KERAS', Anda WAJIB memberikan peringatan tegas bahwa obat ini membutuhkan resep dokter dan tidak boleh digunakan sembarangan.
4. Berikan 1 kalimat tips kesehatan ringan (misal: istirahat yang cukup).
5. Batasi penjelasan Anda maksimal 3 paragraf pendek, sampaikan secara langsung dan profesional tanpa basa-basi panjang.";

        $apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
        
        try {
            if (empty($apiKey)) {
                $this->messages[] = [
                    'role' => 'bot',
                    'content' => 'Maaf, API Key Gemini belum dikonfigurasi. Hasil Forward Chaining manual: ' . (empty($recommendations) ? 'Tidak ada' : $recommendations[0]['name']),
                    'time' => now()->format('H:i')
                ];
                return;
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemPrompt . "\n\nPasien mengatakan: " . $message]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $botReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memahami permintaan Anda saat ini.';
                
                $this->messages[] = [
                    'role' => 'bot',
                    'content' => $botReply,
                    'time' => now()->format('H:i')
                ];
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                $this->messages[] = [
                    'role' => 'bot',
                    'content' => 'Maaf, terjadi kesalahan saat menghubungi layanan AI. ' . (isset($recommendations[0]) ? "Namun, berdasarkan sistem pakar kami, saran terbaik adalah: " . $recommendations[0]['name'] : ""),
                    'time' => now()->format('H:i')
                ];
            }
        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            $this->messages[] = [
                'role' => 'bot',
                'content' => 'Terjadi kesalahan teknis. ' . (isset($recommendations[0]) ? "Saran sistem pakar: " . $recommendations[0]['name'] : ""),
                'time' => now()->format('H:i')
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.chatbot');
    }
}
