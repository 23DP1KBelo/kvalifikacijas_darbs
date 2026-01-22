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
        Schema::create('dance_group_members', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['approved', 'declined', 'waiting'])->default('waiting');
            $table->enum('role', ['dancer', 'leader'])->default('dancer');
            $table->foreignId('user_id')->constrained('app_users')->cascadeOnDelete();
            $table->foreignId('age_group_id')->nullable()->constrained('age_groups');
            $table->foreignId('dance_group_id')->constrained('dance_groups')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dance_group_members');
    }
};
