<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('passes_cinestarcard', function (Blueprint $table) {
            $table->string('first_name')->after('password');
            $table->string('last_name')->after('first_name');
        });
    }

    public function down(): void
    {
        Schema::table('passes_cinestarcard', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
        });
    }
};
