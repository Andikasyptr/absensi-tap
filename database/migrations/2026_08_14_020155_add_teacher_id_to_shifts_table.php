<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            // Pastikan kolom ini diletakkan sesuai kebutuhan, contoh setelah id
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('day')->after('teacher_id'); // Hari (Monday, Tuesday, dst)
            $table->time('shift_start')->after('day'); // Jam Masuk
            $table->time('shift_end')->nullable()->after('shift_start'); // Jam Pulang
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['teacher_id', 'day', 'shift_start', 'shift_end']);
        });
    }
};