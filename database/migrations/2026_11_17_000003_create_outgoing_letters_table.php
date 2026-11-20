<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outgoing_letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number', 100)->unique();
            $table->date('letter_date');
            $table->string('recipient', 200);
            $table->text('subject');
            $table->enum('creation_type', ['Manual (Upload Scan)', 'Template Generator'])->default('Manual (Upload Scan)');
            $table->foreignId('template_id')->nullable()->constrained('letter_templates')->nullOnDelete();
            $table->longText('letter_body_html')->nullable();
            $table->enum('status', ['Draft', 'Terkirim'])->default('Draft');
            $table->string('file_path', 255)->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outgoing_letters');
    }
};
