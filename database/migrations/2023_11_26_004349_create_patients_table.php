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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('document')->index();
            $table->string('cns')->index();
            $table->string('photo')->nullable();
            $table->string('name');
            $table->string('mother_name');
            $table->date('birthdate');
            $table->string('phone');

            $table->unique(['document', 'cns']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
