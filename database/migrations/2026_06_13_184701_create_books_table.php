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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->string('cover_url');
            $table->string('isbn10')->nullable();
            $table->string('isbn13')->nullable();
            $table->integer('page_count')->nullable();
            $table->date('published_date')->nullable();
            $table->string('language');
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->integer('ratings_count')->default(0);
            $table->foreignId('added_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
