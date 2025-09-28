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
        Schema::table('sucess_stories', function (Blueprint $table) {
            if (!Schema::hasColumn('sucess_stories', 'url')) {
                $table->string('url')->nullable()->after('description');
            }
            if (!Schema::hasColumn('sucess_stories', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sucess_stories', function (Blueprint $table) {
            if (Schema::hasColumn('sucess_stories', 'url')) {
                $table->dropColumn('url');
            }
            if (Schema::hasColumn('sucess_stories', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }
        });
    }
};
