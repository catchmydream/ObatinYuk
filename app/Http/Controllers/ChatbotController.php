<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Gejala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function send(Request $request)
    {
        $message = $request->input('message', '');
        if (empty(trim($message))) {
            return response()->json(['reply' => ''], 400);
        }

        // --- PROSES IDENTIFIKASI ---
        $allGejalas = Gejala::all();
        $detectedGejalaIds = [];
        $detectedGejalaNames = [];

        // 1. Helper function untuk normalisasi teks
        $normalize = function ($text) {
            $text = str_replace('-', ' ', $text);
            $text = preg_replace('/[^\w\s]/u', '', $text);
            $text = preg_replace('/\s+/', ' ', $text);
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
                $detectedGejalaIds[] = $gejala->id;
                $detectedGejalaNames[] = $gejala->name;
            }
        }

        $allObats = Obat::with('gejalas')->get();
        $explicitObatIds = [];
        foreach ($allObats as $obat) {
            $normalizedObatName = $normalize($obat->name);
            if (stripos($normalizedMessage, $normalizedObatName) !== false) {
                $explicitObatIds[] = $obat->id;
            }
        }

        // 3. Inference Engine: Cari obat yang cocok dan hitung skor
        $recommendations = [];
        if (!empty($detectedGejalaIds) || !empty($explicitObatIds)) {
            foreach ($allObats as $obat) {
                $obatGejalaIds = $obat->gejalas->pluck('id')->toArray();
                $matchedIds = array_intersect($detectedGejalaIds, $obatGejalaIds);
                $matchCount = count($matchedIds);
                
                $score = 0;
                $reason = "";

                if (in_array($obat->id, $explicitObatIds)) {
                    $score = 100;
                    $reason = "Nama obat '{$obat->name}' disebutkan langsung dalam pesan.";
                } elseif ($matchCount > 0) {
                    $score = ($matchCount / count($detectedGejalaIds)) * 100;
                    $reason = "Cocok dengan gejala [" . implode(', ', $obat->gejalas->whereIn('id', $matchedIds)->pluck('name')->toArray()) . "]";
                }
                
                if ($score > 0) {
                    $recommendations[] = [
                        'name' => $obat->name,
                        'description' => $obat->description,
                        'dosage' => $obat->dosage,
                        'classification' => $obat->classification ?? 'Bebas',
                        'score' => round($score, 2),
                        'reason' => $reason,
                        'match_count' => $matchCount
                    ];
                }
            }

            // Urutkan berdasarkan skor tertinggi, lalu jumlah gejala yang cocok terbanyak
            usort($recommendations, function($a, $b) {
                if ($b['score'] == $a['score']) {
                    return $b['match_count'] <=> $a['match_count'];
                }
                return $b['score'] <=> $a['score'];
            });

            $recommendations = array_slice($recommendations, 0, 3);
        }

        // 6. Siapkan Payload JSON murni untuk dibaca AI
        $contextData = "HASIL REKOMENDASI SISTEM PAKAR:\n";
        if (empty($recommendations)) {
            $contextData .= "- Tidak ditemukan kecocokan obat di database untuk gejala tersebut.\n";
        } else {
            foreach ($recommendations as $index => $rec) {
                $rank = $index + 1;
                $contextData .= "- Peringkat {$rank}: {$rec['name']} (Skor Kecocokan: {$rec['score']}%)\n";
                $contextData .= "  Golongan: " . strtoupper($rec['classification']) . "\n";
                $contextData .= "  Deskripsi: {$rec['description']}\n";
                $contextData .= "  Dosis & Penggunaan: {$rec['dosage']}\n";
                $contextData .= "  Alasan Sistem: {$rec['reason']}\n\n";
            }
        }

        // 7. SYSTEM PROMPT HALODOC STYLE
        $systemPrompt = "Anda adalah seorang Apoteker AI yang ringkas, tegas, namun tetap ramah.
Berikut adalah hasil rekomendasi dari Sistem Pakar Forward Chaining:

{$contextData}

Tugas Anda:
1. Jelaskan hasil rekomendasi obat tersebut kepada pasien sesuai urutan peringkat yang diberikan di atas. Jangan mengubah urutan, skor, atau mengganti rekomendasi utama.
2. Sebutkan nama obat, persentase 'Skor Kecocokan', dan aturan pakainya secara jelas.
3. Jika obat tergolong 'KERAS', Anda WAJIB memberikan peringatan tegas bahwa obat ini membutuhkan resep dokter dan tidak boleh digunakan sembarangan.
4. Berikan 1 kalimat tips kesehatan ringan (misal: istirahat yang cukup).
5. Batasi penjelasan Anda maksimal 3 paragraf pendek, sampaikan secara langsung dan profesional tanpa basa-basi panjang.";

        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['reply' => 'API Key belum dikonfigurasi.']);
        }

        // 8. Kirim ke Gemini 1.5 Flash
        try {
            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'system_instruction' => [ // Menggunakan fitur system_instruction yang benar untuk Gemini 1.5
                    'parts' => [['text' => $systemPrompt]]
                ],
                'contents' => [
                    [
                        'role'  => 'user',
                        'parts' => [['text' => "Data Sistem Pakar (JSON): \n{$contextData}\n\nKeluhan Pasien: {$message}"]]
                    ]
                ],
                'generationConfig' => [
                    'temperature'      => 0.3, // Diturunkan agar respon medis lebih stabil dan tidak berhalusinasi
                    'maxOutputTokens'  => 2048,
                ],
            ]);

            if ($response->successful()) {
                $data  = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat merespons saat ini.';
                return response()->json(['reply' => $reply]);
            }

            Log::error('Gemini error: ' . $response->body());
            return response()->json(['reply' => 'Maaf, terjadi gangguan pada asisten AI. Silakan konsultasi ke dokter.']);
            
        } catch (\Exception $e) {
            Log::error('Chatbot exception: ' . $e->getMessage());
            return response()->json(['reply' => 'Terjadi kesalahan server internal.']);
        }
    }
}