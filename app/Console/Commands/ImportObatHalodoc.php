<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MedicineScraperService;
use App\Models\Obat;
use Illuminate\Support\Facades\Log;

class ImportObatHalodoc extends Command
{
    protected $signature = 'obat:import {name?} {--limit=100}';
    protected $description = 'Import data obat otomatis dari K24Klik';

    protected $drugs = [
        // Umum & Nyeri
        'Paracetamol 500 mg', 'Panadol Biru', 'Bodrex', 'Saridon', 'Biogesic', 'Ibuprofen 400 mg', 'Asam Mefenamat 500 mg', 'Ponstan', 'Neuralgin Rx', 'Voltaren Emulgel',
        // Flu & Batuk
        'Siladex Mucolytic', 'OBH Combi Batuk Flu', 'Vicks Formula 44', 'Woods Peppermint Expectorant', 'Mixagrip Flu', 'Sanaflu', 'Procold Flu', 'Actifed Kuning', 'Actifed Merah', 'Actifed Hijau', 'Decolsin', 'Tremenza',
        // Pencernaan & Lambung
        'Promag Tablet', 'Mylanta Cair', 'Polysilane', 'Plantacid', 'Inpepsa Syrup', 'Lansoprazole 30 mg', 'Omeprazole 20 mg', 'Braxidin', 'Buscopan', 'New Diatabs', 'Entrostop', 'Oralit', 'Dulcolax Tablet', 'Microlax',
        // Alergi & Kulit
        'CTM', 'Incidal-OD', 'Cetirizine 10 mg', 'Lerzin', 'Betadine Solution', 'Bioplacenton', 'Kalpanax Salep', 'Daktarin Krim', 'Canesten', 'Hydrocortisone Salep', 'Acyclovir Krim', 'Claritin',
        // Antibiotik (OBAT KERAS)
        'Amoxicillin 500 mg', 'Cefadroxil 500 mg', 'Ciprofloxacin 500 mg', 'Clindamycin 300 mg', 'Erythromycin 500 mg', 'Thiamphenicol 500 mg', 'Azithromycin 500 mg',
        // Suplemen & Vitamin
        'Enervon-C', 'Imboost Force', 'Sangobion', 'Neurobion Forte', 'CDR Tablet Hisap', 'Redoxon', 'Caviplex', 'Becom-C', 'Renavit', 'Vidoran Smart',
        // Mata & Telinga
        'Insto Regular', 'Rohto Cool', 'Cendo Xitrol', 'Cendo Fenicol', 'Vital Ear Oil', 'Erlamycetin Tetes Mata',
        // Penyakit Kronis (OBAT KERAS)
        'Amlodipine 5 mg', 'Candesartan 8 mg', 'Metformin 500 mg', 'Glibenclamide 5 mg', 'Atorvastatin 20 mg', 'Simvastatin 10 mg', 'Allopurinol 100 mg', 'Salbutamol 2 mg', 'Ventolin Inhaler',
        // Lain-lain
        'Habbatusauda', 'Minyak Kayu Putih Cap Lang', 'Fresh Care', 'Tolak Angin', 'Antangin JRG', 'Salonpas Koyo', 'Counterpain Cream', 'Hansaplast', 'Betadine Kumur'
    ];

    public function handle(MedicineScraperService $service)
    {
        $limit = $this->option('limit');
        $drugName = $this->argument('name');
        
        $targetDrugs = $drugName ? [$drugName] : $this->drugs;

        $this->info("Memulai import data obat dari K24Klik...");
        
        $count = 0;
        foreach ($targetDrugs as $name) {
            if ($count >= $limit) break;

            $this->info("Mencari: $name...");
            
            $data = $service->searchAndImport($name);

            if ($data) {
                Obat::updateOrCreate(
                    ['name' => $data['name']],
                    [
                        'description' => $data['description'],
                        'dosage' => $data['dosage'],
                        'classification' => $data['classification'],
                        'image' => $data['image'] ?? 'obat-images/default.png',
                        'price' => rand(5000, 150000), // Harga estimasi
                        'stock' => rand(10, 100),
                    ]
                );
                $this->info("✅ Berhasil: {$data['name']} (" . strtoupper($data['classification']) . ")");
                $count++;
            } else {
                $this->warn("❌ Gagal menemukan: $name");
            }

            // Delay sedikit agar tidak terkena rate limit
            sleep(1);
        }

        $this->info("Selesai! $count obat berhasil di-import.");
    }
}
