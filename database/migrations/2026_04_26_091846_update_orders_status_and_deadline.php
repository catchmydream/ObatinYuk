<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add payment deadline column
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('payment_deadline')->nullable()->after('payment_proof');
        });

        // Update status enum to include new values
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'Menunggu Pembayaran',
            'Menunggu Verifikasi',
            'Diproses',
            'Dikirim',
            'Selesai',
            'Dibatalkan'
        ) NOT NULL DEFAULT 'Menunggu Pembayaran'");
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_deadline');
        });
    }
};
