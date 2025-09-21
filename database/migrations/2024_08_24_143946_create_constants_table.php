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
        Schema::create('constants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name')->nullable();
            $table->json('description')->nullable();
            $table->string('value')->nullable();
            $table->string('constant_name');
            $table->string('icon')->nullable();
            $table->integer('order')->nullable();
            $table->string('color')->nullable();
            $table->string('module')->index();
            $table->string('field')->index();
            $table->boolean('active')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('constants')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constants');
    }
};
