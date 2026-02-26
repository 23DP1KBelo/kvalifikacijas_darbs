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
        Schema::create('dance_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('city');
            $table->string('address');
            $table->string('picture')->nullable();
            $table->string('approval');
            $table->enum('status', ['approved', 'declined', 'waiting'])->default('waiting');
            $table->enum('genre', ['lyrical dance', 'ballet', 'contemporary dance', 'folk dance', 'hip hop', 'other'])->default('other');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dance_groups');
    }
};
