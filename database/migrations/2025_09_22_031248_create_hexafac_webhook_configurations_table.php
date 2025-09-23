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
        Schema::create('hexafac_webhook_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('hexafac_client_applications')->onDelete('cascade');
            $table->string('url');
            $table->string('secret')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hexafac_webhook_configurations');
    }
};
