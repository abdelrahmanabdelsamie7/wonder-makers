<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('position');
            $table->string('image');
            $table->string('facebook_link')->nullable();
            $table->string('linkedIn_link')->nullable();
            $table->string('phone',15);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};