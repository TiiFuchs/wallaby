<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passes_cinestarcard', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('customer_number')->nullable();
            $table->float('premium_points')->nullable();
            $table->foreignIdFor(\App\Models\CineStarCard\Cinema::class, 'regular_cinema_id')->nullable();
            $table->timestamps();

            $table->unique('username');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passes_cinestarcard');
    }
};
