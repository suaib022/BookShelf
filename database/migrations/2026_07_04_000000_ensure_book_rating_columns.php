<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure books table has avg_rating and ratings_count columns
     * (they should exist already but we add defaults to be safe).
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'avg_rating')) {
                $table->decimal('avg_rating', 3, 2)->default(0)->after('description');
            }
            if (!Schema::hasColumn('books', 'ratings_count')) {
                $table->unsignedInteger('ratings_count')->default(0)->after('avg_rating');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumnIfExists('avg_rating');
            $table->dropColumnIfExists('ratings_count');
        });
    }
};
