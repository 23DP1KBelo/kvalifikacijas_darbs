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
            $table->foreignId('user_id')->references('id')->on('app_users');
            $table->foreignId('age_group_id')->nullable()->references('id')->on('age_groups');
            $table->foreignId('dance_group_id')->references('id')->on('dance_groups');
            $table->timestamps();
            $table->softDeletes();
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
