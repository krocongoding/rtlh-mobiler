<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->foreignId('settlement_condition_id')
                ->nullable()
                ->after('survey_year');

            $table->foreignId('room_function_id')
                ->nullable()
                ->after('settlement_condition_id');

            $table->foreignId('ownership_status_id')
                ->nullable()
                ->after('room_function_id');

            $table->foreignId('land_status_id')
                ->nullable()
                ->after('ownership_status_id');
        });
    }

    public function down(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->dropColumn([
                'settlement_condition_id',
                'room_function_id',
                'ownership_status_id',
                'land_status_id',
            ]);
        });
    }
};