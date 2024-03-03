<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sponsor_id')->nullable()->constrained('sponsors')->onDelete('cascade');
            $table->string('name');
            $table->string('number');
            $table->string('address');
            $table->boolean('renting_status')->default(false);
            $table->string('front_image_path');
            $table->string('back_image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
