<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passes_dticket', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->dateTime('valid_in');
            $table->text('barcode');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passes_dticket');
    }
};
