<?php

use App\Models\MediaType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_types', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('name', 10)->unique();
        });

        MediaType::insert([
            ['id' => 1, 'name' => 'movie'],
            ['id' => 2, 'name' => 'tv'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('media_types');
    }
};
