<?php

use App\Enums\V1\SidebarBox\PostSelectionType;
use App\Enums\V1\SidebarBox\SidebarPlacement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sidebar_boxes', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->unsignedInteger('sort_order')->default(0);

            $table->string('placement')->default(SidebarPlacement::NONE);

            $table->string('post_selection_type')->default(PostSelectionType::NONE);

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->foreignId('tag_id')
                ->nullable()
                ->constrained('tags')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('posts_limit')
                ->nullable();

            $table->timestamps();
        });

        DB::statement('
            ALTER TABLE sidebar_boxes
            ADD CONSTRAINT sidebar_boxes_posts_limit_check
            CHECK (posts_limit IS NULL OR posts_limit BETWEEN 1 AND 20)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_boxes');
    }
};
