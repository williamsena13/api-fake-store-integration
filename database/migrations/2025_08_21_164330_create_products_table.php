<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id')->unique()->index();
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2)->index();
            $table->string('image_url');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent()->comment('The created_at timestamp registered');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->comment('The updated_at timestamp registered')->nullable()->default(null);
            $table->softDeletes();

            $table->index('category_id', 'idx_products_category_id');
            $table->index('title', 'idx_products_title');
            $table->index(['price', 'category_id'], 'idx_products_price_category');
            $table->index(['created_at', 'category_id'], 'idx_products_created_category');
            $table->index('deleted_at', 'idx_products_deleted_at');

            $table->index(['category_id', 'price', 'deleted_at'], 'idx_products_filter_combo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
