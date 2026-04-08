<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('list_type')->index();
            $table->string('name', 100);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'list_type', 'is_default']);
            $table->foreign('list_type')->references('id')->on('list_types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_lists');
    }
};
