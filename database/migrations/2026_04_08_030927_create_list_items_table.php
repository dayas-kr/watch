<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('user_lists')->cascadeOnDelete();
            $table->unsignedInteger('media_id');
            $table->unsignedTinyInteger('media_type');
            $table->timestamp('added_at')->useCurrent();

            $table->unique(['list_id', 'media_id', 'media_type']);
            $table->foreign('media_type')->references('id')->on('media_types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('list_items');
    }
};
