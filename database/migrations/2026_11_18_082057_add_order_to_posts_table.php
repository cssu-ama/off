<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('order')->nullable();
        });

        DB::statement(<<<'SQL'
            WITH ordered_posts AS (
                SELECT
                    id,
                    ROW_NUMBER() OVER (ORDER BY id DESC)::integer AS position
                FROM posts
            )
            UPDATE posts
            SET "order" = ordered_posts.position
            FROM ordered_posts
            WHERE posts.id = ordered_posts.id;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
