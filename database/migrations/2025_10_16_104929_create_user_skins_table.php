<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_skins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('skin_uuid')->index();
            $table->string('chroma_uuid')->nullable()->index();
            $table->boolean('owned')->default(true);
            $table->boolean('wishlist')->default(false);
            $table->json('metadata')->nullable(); // snapshot of skin data
            $table->timestamps();

            $table->unique(['user_id','skin_uuid','chroma_uuid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_skins');
    }
};