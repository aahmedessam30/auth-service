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
        Schema::create('token_blacklist', function (Blueprint $table) {
            $table->string('jti', 36)->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('expires_at')->index();
            $table->timestamp('blacklisted_at');
            $table->string('reason', 100)->nullable();

            $table->index(['user_id', 'blacklisted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_blacklist');
    }
};
