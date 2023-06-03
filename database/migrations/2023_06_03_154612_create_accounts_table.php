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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('user_id')->index();
            $table->unsignedMediumInteger('currency_id')->index();
            $table->string('uuid', 36)->unique();
            $table->unsignedDecimal('balance', 10, 2);
            $table->index(['user_id', 'currency_id']);
            $table->unique(['user_id', 'currency_id']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
