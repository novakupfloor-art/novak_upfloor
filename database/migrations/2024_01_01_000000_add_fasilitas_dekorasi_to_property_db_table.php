<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('property_db')) {
            Schema::table('property_db', function (Blueprint $table) {
                if (!Schema::hasColumn('property_db', 'fasilitas_dekorasi')) {
                    $table->text('fasilitas_dekorasi')->nullable()->after('fasilitas_terdekat');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_db')) {
            Schema::table('property_db', function (Blueprint $table) {
                if (Schema::hasColumn('property_db', 'fasilitas_dekorasi')) {
                    $table->dropColumn('fasilitas_dekorasi');
                }
            });
        }
    }
};
