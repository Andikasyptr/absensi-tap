<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Masukkan data default awal
        DB::table('settings')->insert([
            ['key' => 'school_foundation', 'value' => 'Pemerintah Provinsi / Yayasan Pendidikan', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_name', 'value' => 'SMK Hijau Muda', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_tagline', 'value' => 'Sistem Informasi Presensi Cepat & Terpadu (SIFAT)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_address', 'value' => 'Jl. Pendidikan No. 01, Telp: (021) 5558899, Email: info@hijaumuda.sch.id', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'headmaster_name', 'value' => 'H. Ahmad Fauzi, S.Pd., M.Pd.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'headmaster_nip', 'value' => 'NIP. 19750612 200003 1 005', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'city_location', 'value' => 'Jakarta', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};