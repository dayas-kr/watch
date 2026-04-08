<?php

use App\Models\ListType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_types', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('name', 50)->unique();
        });

        ListType::insert([
            ['id' => 1, 'name' => 'watchlist'],
            ['id' => 2, 'name' => 'favorites'],
            ['id' => 3, 'name' => 'watched'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('list_types');
    }
};
