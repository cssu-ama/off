<?php

use App\Enums\V1\Banner\BannerPlacement;
use App\Enums\V1\Banner\BannerStatus;
use App\Enums\V1\Banner\LinkRelation;
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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->string('link');

            $table->string('placement')
                ->default(BannerPlacement::NONE);

            $table->string('rel')
                ->default(LinkRelation::NOFOLLOW);

            $table->string('status')
                ->default(BannerStatus::INACTIVE);

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
