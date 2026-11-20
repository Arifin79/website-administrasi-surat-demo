<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->id();
            $table->string('agenda_number', 60)->unique();
            $table->string('reference_number', 100);
            $table->date('letter_date');
            $table->date('received_date');
            $table->string('sender', 200);
            $table->text('subject');
            $table->enum('status', ['Diterima', 'Diproses', 'Selesai'])->default('Diterima');
            $table->string('file_path', 255)->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_letters');
    }
};
