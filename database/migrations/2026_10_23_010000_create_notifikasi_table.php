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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('target_role', 20)->nullable()->index(); // 'biro' atau 'user'
            $table->string('judul', 150);
            $table->text('pesan');
            $table->string('tipe', 50)->index();
            $table->string('url', 255)->nullable();
            $table->boolean('dibaca')->default(false)->index();
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
