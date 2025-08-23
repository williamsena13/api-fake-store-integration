<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->uuid('external_id');
            $table->timestamp('created_at')->useCurrent()->comment('The created_at timestamp registered');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->comment('The updated_at timestamp registered')->nullable()->default(null);
            $table->softDeletes();

            $table->index('name', 'idx_categories_name');
            $table->unique('name', 'unq_categories_name');
            $table->index('deleted_at', 'idx_categories_deleted_at');
            $table->index(['name', 'deleted_at'], 'idx_categories_name_deleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
