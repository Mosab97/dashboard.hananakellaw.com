<?php

use App\Models\Attachment;
use App\Models\Constant;
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
        if (Schema::hasTable(Attachment::ui['table'])) {
            Schema::dropIfExists(Attachment::ui['table']);
        }
        Schema::create(Attachment::ui['table'], function (Blueprint $table) {
            $table->id();
            $table->foreignId('attachment_type_id')->nullable()->constrained(Constant::ui['table']);
            $table->string('file_path')->nullable();
            $table->string('file_hash')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->string('file_size')->nullable();
            $table->string('file_extension')->nullable();
            $table->nullableMorphs('attachable');
            $table->string('source')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
