<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->foreignId('device_id')->constrained();
            $table->foreignId('pass_id')->constrained();
            $table->timestamps();

            $table->primary(['device_id', 'pass_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
