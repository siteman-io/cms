<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->references('id')->on('menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->references('id')->on($table->getTable())->nullOnDelete();
            $table->nullableMorphs('linkable');
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('target', 10)->default('_self');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('menu_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->references('id')->on('menus')->cascadeOnDelete();
            $table->string('location')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_locations');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
