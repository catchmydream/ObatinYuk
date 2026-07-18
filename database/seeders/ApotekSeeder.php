<?php

namespace Database\Seeders;

use App\Models\Gejala;
use App\Models\Obat;
use Illuminate\Database\Seeder;

class ApotekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Data Gejala Dulu
        $gejalas = [
            'Batuk Berdahak' => Gejala::firstOrCreate(['name' => 'Batuk Berdahak']),
            'Batuk Kering' => Gejala::firstOrCreate(['name' => 'Batuk Kering']),
            'Flu' => Gejala::firstOrCreate(['name' => 'Flu']),
            'Pusing' => Gejala::firstOrCreate(['name' => 'Pusing']),
            'Sakit Kepala' => Gejala::firstOrCreate(['name' => 'Sakit Kepala']),
            'Mual' => Gejala::firstOrCreate(['name' => 'Mual']),
            'Sakit Perut' => Gejala::firstOrCreate(['name' => 'Sakit Perut']),
            'Asam Lambung Naik' => Gejala::firstOrCreate(['name' => 'Asam Lambung Naik']),
        ];

        // 2. Insert Data Obat beserta Gambar dan Relasinya
        
        // Obat Batuk
        $obatBatuk = Obat::firstOrCreate(
            ['name' => 'Siladex Mucolytic'],
            [
                'description' => 'Sirup obat batuk berdahak, membantu mengencerkan dahak sehingga mudah dikeluarkan.',
                'image' => 'obat-images/obat_batuk.png'
            ]
        );
        $obatBatuk->gejalas()->syncWithoutDetaching([
            $gejalas['Batuk Berdahak']->id,
            $gejalas['Flu']->id
        ]);

        // Obat Pusing / Sakit Kepala
        $obatPusing = Obat::firstOrCreate(
            ['name' => 'Bodrex Extra'],
            [
                'description' => 'Kaplet pereda nyeri yang diformulasikan khusus untuk meredakan sakit kepala mencengkeram dan sakit gigi.',
                'image' => 'obat-images/obat_pusing.png'
            ]
        );
        $obatPusing->gejalas()->syncWithoutDetaching([
            $gejalas['Pusing']->id,
            $gejalas['Sakit Kepala']->id
        ]);

        // Obat Maag
        $obatMaag = Obat::firstOrCreate(
            ['name' => 'Promag Tablet'],
            [
                'description' => 'Tablet kunyah untuk meredakan mual, nyeri lambung, dan perih akibat kelebihan asam lambung.',
                'image' => 'obat-images/obat_maag.png'
            ]
        );
        $obatMaag->gejalas()->syncWithoutDetaching([
            $gejalas['Mual']->id,
            $gejalas['Sakit Perut']->id,
            $gejalas['Asam Lambung Naik']->id
        ]);
        
        $this->command->info('Data obat dan gejala berhasil di-seed!');
    }
}
