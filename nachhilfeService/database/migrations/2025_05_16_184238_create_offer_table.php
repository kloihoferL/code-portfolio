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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('description');
            $table->foreignId('course_id')->constrained()->onDelete('cascade'); //angebot löschen wenn kurs gelöscht wird
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); //angebot löschen wenn user gelöscht wird
            $table->string('comment')->default('');
            $table->boolean('booked')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
