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
        Schema::table('free_site_requests', function (Blueprint $table) {
    $table->string('plan')->default('starter');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('free_site_requests', function (Blueprint $table) {
        $table->dropColumn('plan');
    });
    }
};
