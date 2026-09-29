<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('school_classes', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // Contoh: XII TKJ 1, XI RPL 2
        $table->string('level')->nullable(); // Contoh: X, XI, XII
        $table->text('description')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
