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
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->unique();
            $table->string('base_url')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_key_param')->nullable();
            $table->string('method')->nullable();
            $table->string(        'default_params')->nullable();
            $table->boolean('has_pagination')->default(false);
            $table->boolean('uses_header')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
