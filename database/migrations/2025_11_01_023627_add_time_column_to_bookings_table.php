<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BookType;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_appointments', function (Blueprint $table) {
            if(!Schema::hasColumn('book_appointments', 'time')) {
                $table->time('time')->after('date')->nullable();
            }
            if(!Schema::hasColumn('book_appointments', 'book_type')) {
                $table->enum('book_type', BookType::toArray())->nullable()->after('time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_appointments', function (Blueprint $table) {
            if(Schema::hasColumn('book_appointments', 'time')) {
                $table->dropColumn('time');
            }
            if(Schema::hasColumn('book_appointments', 'book_type')) {
                $table->dropColumn('book_type');
            }
        });
    }
};
