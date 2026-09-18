<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable()->unique();
            $table->unsignedTinyInteger('level')->default(1)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
