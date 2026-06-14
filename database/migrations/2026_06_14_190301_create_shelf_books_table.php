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
        Schema::create('shelf_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelf_id')->constrained('shelves')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('date_added')->useCurrent();
            $table->timestamp('date_started')->nullable();
            $table->timestamp('date_finished')->nullable();
            $table->integer('progress_pages')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'shelf_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelf_books');
    }
};
