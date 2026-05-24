<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->unsignedSmallInteger('duration')->nullable();
            $table->string('type')->default('1-on-1');
            $table->string('title');
            $table->enum('status', ['scheduled', 'completed'])->default('scheduled');
            $table->text('topics')->nullable();
            $table->text('accomplished')->nullable();
            $table->text('action_items')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
