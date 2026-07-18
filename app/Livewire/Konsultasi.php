<?php

namespace App\Livewire;

use App\Models\Obat;
use App\Models\Gejala;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Konsultasi extends Component
{
    public $messages = [];
    public $inputMessage = '';
    public $isLoading = false;

    // Expert System State for Sidebar
    public $detectedGejalas = [];
    public $recommendations = [];
    public $evaluationSteps = [];
    public $hasAnalyzed = false;

    public function mount()
    {
        $this->messages[] = [
            'role' => 'bot',
            'content' => "Selamat datang di **Konsultasi Kesehatan ObatinYuk**!\n\nSaya adalah Asisten Apoteker Digital yang dilengkapi dengan **Sistem Pakar Forward Chaining**. Ceritakan keluhan, gejala penyakit, atau kebutuhan obat/vitamin Anda secara bebas. Sistem kami akan menganalisis gejala Anda dan memberikan rekomendasi obat paling akurat dari database kami.",
            'time' => now()->format('H:i')
        ];
    }

    public function sendMessage($customMessage = null)
    {
        $userMessage = $customMessage ?? trim($this->inputMessage);

        if ($userMessage === '') {
            return;
        }

        $this->inputMessage = '';
        $this->messages[] = [
            'role'    => 'user',
            'content' => $userMessage,
            'time'    => now()->format('H:i'),
        ];

        $this->analyzeAndRespond($userMessage);
    }

    public function analyzeAndRespond($message)
    {
        $this->isLoading = true;

        try {
            // --- 1. FORWARD CHAINING ENGINE ---
            $allGejalas = Gejala::all();
            $matchedGejalaIds = [];
            $matchedGejalaNames = [];

            $isAskingForVitamin = (stripos($message, 'vitamin') !== false || stripos($message, 'suplemen') !== false);
            $isAskingForHerbal = (stripos($message, 'herbal') !== false || stripos($message, 'jamu') !== false);

            // Fact Extraction: Matching symptoms in user query
            $normalize = function ($text) {
                $text = str_replace('-', ' ', $text); // Ganti tanda hubung dengan spasi
                $text = preg_replace('/[^\w\s]/u', '', $text); // Hapus tanda baca/karakter non-alfanumerik kecuali spasi
                $text = preg_replace('/\s+/', ' ', $text); // Satukan spasi berlebih
                return strtolower(trim($text));
            };

            $normalizedMessage = $normalize($message);

            // Pemetaan Sinonim Gejala
            $synonymMapping = [
                'meredakan pilek' => ['pilek', 'flu', 'bersin', 'ingusan', 'meler'],
                'hidung tersumbat' => ['hidung tersumbat', 'hidung mampet', 'hidung buntu', 'susah napas'],
                'batuk berdahak' => ['batuk berdahak', 'batuk lendir', 'batuk basah'],
                'demam' => ['demam', 'panas', 'meriang', 'fever'],
                'batuk kering' => ['batuk kering', 'dry cough'],
            ];

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
                    $matchedGejalaIds[] = $gejala->id;
                    $matchedGejalaNames[] = $gejala->name;
                }
            }

            $this->detectedGejalas = $matchedGejalaNames;
            $this->evaluationSteps = [];
            $this->evaluationSteps[] = "Langkah 1: Ekstraksi fakta dari seluruh percakapan pengguna.";
            if (!empty($matchedGejalaNames)) {
                $this->evaluationSteps[] = "Terdeteksi " . count($matchedGejalaNames) . " gejala: " . implode(', ', $matchedGejalaNames);
            } else {
                $this->evaluationSteps[] = "Tidak ada spesifikasi nama gejala standar yang cocok secara eksplisit.";
            }

            // Inference Engine: Calculating Obat scores
            $recs = [];
            $allObats = Obat::with('gejalas')->get();

            if (!empty($matchedGejalaIds)) {
                $this->evaluationSteps[] = "Langkah 2: Evaluasi Aturan (Rules) kecocokan obat di database berdasarkan gejala terdeteksi.";
                foreach ($allObats as $obat) {
                    $obatGejalaIds = $obat->gejalas->pluck('id')->toArray();
                    $intersect = array_intersect($matchedGejalaIds, $obatGejalaIds);
                    $matchCount = count($intersect);

                    if ($matchCount > 0) {
                        $totalUserSymptoms = count($matchedGejalaIds);

                        if ($totalUserSymptoms === 0) {
                            $score = 0;
                        } else {
                            $score = ($matchCount / $totalUserSymptoms) * 100;
                        }
                        
                        $matchedNames = $obat->gejalas->whereIn('id', $intersect)->pluck('name')->toArray();

                        $recs[] = [
                            'id' => $obat->id,
                            'name' => $obat->name,
                            'category' => $obat->category,
                            'classification' => $obat->classification ?? 'Bebas',
                            'price' => $obat->price,
                            'stock' => $obat->stock,
                            'image' => $obat->image,
                            'description' => $obat->description,
                            'dosage' => $obat->dosage,
                            'aturan_pakai' => $obat->aturan_pakai,
                            'score' => round($score, 1),
                            'matched_gejala' => $matchedNames,
                            'match_count' => $matchCount,
                            'reason' => "Cocok dengan " . count($matchedNames) . " gejala (" . implode(', ', $matchedNames) . ")"
                        ];
                    }
                }
            }

            // Category matching (Vitamin/Herbal)
            if ($isAskingForVitamin || $isAskingForHerbal) {
                $this->evaluationSteps[] = "Langkah 2b: Evaluasi Aturan Kategori Spesifik (Vitamin/Herbal).";
                $targetCategories = [];
                if ($isAskingForVitamin) $targetCategories = array_merge($targetCategories, ['vitamin', 'suplemen']);
                if ($isAskingForHerbal) $targetCategories[] = 'herbal';

                $vits = Obat::whereIn('category', $targetCategories)
                    ->orWhere('name', 'like', '%vitamin%')
                    ->orWhere('name', 'like', '%vit %')
                    ->get()->take(3);

                foreach ($vits as $vit) {
                    if (!collect($recs)->contains('id', $vit->id)) {
                        $recs[] = [
                            'id' => $vit->id,
                            'name' => $vit->name,
                            'category' => $vit->category,
                            'classification' => $vit->classification ?? 'Bebas',
                            'price' => $vit->price,
                            'stock' => $vit->stock,
                            'image' => $vit->image,
                            'description' => $vit->description,
                            'dosage' => $vit->dosage,
                            'aturan_pakai' => $vit->aturan_pakai,
                            'score' => 100,
                            'matched_gejala' => [],
                            'match_count' => 0,
                            'reason' => "Rekomendasi kategori " . ucfirst($vit->category)
                        ];
                    }
                }
            }

            // Sort recommendations by score descending, then match_count descending
            usort($recs, function($a, $b) {
                if ($b['score'] == $a['score']) {
                    $aCount = $a['match_count'] ?? 0;
                    $bCount = $b['match_count'] ?? 0;
                    return $bCount <=> $aCount;
                }
                return $b['score'] <=> $a['score'];
            });

            $this->recommendations = array_slice($recs, 0, 5);
            $this->hasAnalyzed = true;
            $this->evaluationSteps[] = "Langkah 3: Perangkaian Keputusan Selesai. Ditemukan " . count($this->recommendations) . " rekomendasi terbaik.";

            $hasObatKeras = false;
            if (!empty($this->recommendations)) {
                foreach ($this->recommendations as $rec) {
                    if (isset($rec['classification']) && strcasecmp($rec['classification'], 'keras') === 0) {
                        $hasObatKeras = true;
                    }
                }
            }

            // --- 2. GENERATIVE AI AI RESPONSE ---
            $contextData = "HASIL SISTEM PAKAR FORWARD CHAINING:\n";
            if (empty($this->recommendations)) {
                $contextData .= "- Tidak ditemukan kecocokan obat di database untuk gejala tersebut.\n";
            } else {
                foreach ($this->recommendations as $index => $rec) {
                    $rank = $index + 1;
                    $contextData .= "- Peringkat {$rank}: {$rec['name']} (Skor Kecocokan: {$rec['score']}%)\n";
                    $contextData .= "  Kategori/Golongan: " . ucfirst($rec['category']) . " / " . strtoupper($rec['classification']) . "\n";
                    $contextData .= "  Alasan sistem: {$rec['reason']}\n";
                    $contextData .= "  Aturan Pakai: " . ($rec['aturan_pakai'] ? str_replace('_', ' ', $rec['aturan_pakai']) : 'Sesuai petunjuk kemasan') . "\n";
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

            $historyText = "";
            foreach ($this->messages as $msg) {
                $sender = $msg['role'] === 'user' ? 'Pasien' : 'Apoteker AI';
                $historyText .= "{$sender}: {$msg['content']}\n";
            }

            $apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
            $reply = '';

            if (!empty($apiKey)) {
                try {
                    $response = Http::timeout(30)->withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => $systemPrompt . "\n\nBerikut adalah riwayat percakapan sejauh ini:\n" . $historyText . "\nSilakan berikan tanggapan apoteker Anda untuk keluhan terbaru pasien tersebut secara sinkron dengan rekomendasi teratas di atas."]]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.2, // Lower temperature to ensure strict alignment with the provided data
                            'maxOutputTokens' => 2048,
                        ]
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    } else {
                        Log::error("Gemini AI API Error: " . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error("Gemini AI API Exception: " . $e->getMessage());
                }
            }

            // Fallback narrative if AI API is unavailable or returns empty
            if (empty($reply)) {
                $reply = $this->generateOfflineFallbackReply();
            } else {
                // Append the structured top recommendations list
                if (!empty($this->recommendations)) {
                    $recommendationsList = "\n\n**Daftar Rekomendasi Obat:**\n";
                    foreach ($this->recommendations as $index => $rec) {
                        $rank = $index + 1;
                        $recommendationsList .= "**{$rank}. {$rec['name']}** (Skor Kecocokan: **{$rec['score']}%**)\n";
                        $recommendationsList .= "• Golongan: " . strtoupper($rec['classification']) . "\n";
                        $recommendationsList .= "• Aturan Pakai: " . ($rec['aturan_pakai'] ? str_replace('_', ' ', $rec['aturan_pakai']) : 'Sesuai petunjuk kemasan') . "\n";
                        $recommendationsList .= "• Alasan Sistem: {$rec['reason']}\n\n";
                    }
                    $reply .= $recommendationsList;
                }

                // Append warning if there's any hard medicine (Obat Keras)
                if ($hasObatKeras) {
                    $warningSuffix = "\n\n⚠️ **Peringatan:** Hasil analisis mendeteksi adanya **Obat Keras**. Penggunaan obat ini wajib di bawah pengawasan dokter dan memerlukan resep dokter resmi.";
                    if (stripos($reply, 'resep dokter') === false && stripos($reply, 'obat keras') === false) {
                        $reply .= $warningSuffix;
                    } else {
                        $reply .= "\n\n*(Catatan: Obat yang direkomendasikan tergolong Obat Keras dan membutuhkan resep dokter)*";
                    }
                }
            }

            $this->messages[] = [
                'role' => 'bot',
                'content' => $reply,
                'time' => now()->format('H:i')
            ];

        } catch (\Exception $e) {
            Log::error("Error in Konsultasi component: " . $e->getMessage());
            
            $reply = $this->generateOfflineFallbackReply();

            $this->messages[] = [
                'role' => 'bot',
                'content' => $reply,
                'time' => now()->format('H:i')
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function addToCart($obatId)
    {
        if (!Auth::check()) {
            $this->dispatch('swal:error', [
                'title' => 'Perhatian',
                'text' => 'Silakan masuk (login) terlebih dahulu untuk menambah obat ke keranjang.'
            ]);
            return;
        }

        $userId = Auth::id();
        $cartItem = CartItem::where('user_id', $userId)->where('obat_id', $obatId)->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            CartItem::create([
                'user_id' => $userId,
                'obat_id' => $obatId,
                'quantity' => 1
            ]);
        }

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Obat rekomendasi berhasil ditambahkan ke keranjang belanja Anda.'
        ]);
    }

    public function clearChat()
    {
        $this->messages = [];
        $this->detectedGejalas = [];
        $this->recommendations = [];
        $this->evaluationSteps = [];
        $this->hasAnalyzed = false;

        $this->messages[] = [
            'role' => 'bot',
            'content' => "Riwayat percakapan telah dibersihkan. Silakan sampaikan keluhan kesehatan baru Anda.",
            'time' => now()->format('H:i')
        ];
    }

    private function generateOfflineFallbackReply()
    {
        if (empty($this->recommendations)) {
            return "Terima kasih telah berkonsultasi. Berdasarkan analisis Sistem Pakar kami, saat ini belum ditemukan obat di database yang cocok secara spesifik dengan keluhan Anda. Kami sangat menyarankan Anda untuk berkonsultasi langsung dengan dokter untuk penanganan medis lebih tepat.";
        }

        $reply = "Mohon maaf, koneksi ke asisten AI kami sedang terganggu (offline). Namun, **Sistem Pakar Forward Chaining** kami tetap aktif secara lokal.\n\nBerikut adalah **Top 5 Rekomendasi Obat** terbaik berdasarkan kecocokan gejala Anda:\n\n";

        foreach ($this->recommendations as $index => $rec) {
            $rank = $index + 1;
            $reply .= "**{$rank}. {$rec['name']}** (Skor Kecocokan: **{$rec['score']}%**)\n";
            $reply .= "• Golongan: " . strtoupper($rec['classification']) . "\n";
            $reply .= "• Aturan Pakai: " . ($rec['aturan_pakai'] ? str_replace('_', ' ', $rec['aturan_pakai']) : 'Sesuai petunjuk kemasan') . "\n";
            $reply .= "• Alasan Sistem: {$rec['reason']}\n\n";
        }

        $reply .= "Anda dapat melihat detail obat atau langsung memasukkannya ke keranjang melalui panel di sebelah kanan.";
        return $reply;
    }

    public function render()
    {
        return view('livewire.konsultasi')->layout('layouts.app', ['title' => 'Konsultasi AI & Sistem Pakar']);
    }
}
