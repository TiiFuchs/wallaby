<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passes', function (Blueprint $table) {
            $table->id();
            $table->string('pass_type_id');
            $table->string('serial_number');
            $table->string('authentication_token');
            $table->timestamp('last_requested_at');
            $table->unsignedBigInteger('details_id')->nullable();
            $table->string('details_type')->nullable();
            $table->timestamps();

            $table->unique([
                'pass_type_id', 'serial_number',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passes');
    }
};
