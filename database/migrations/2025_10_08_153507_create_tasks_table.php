<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->dateTime('due_date')->nullable();
            $table->enum('priority', ['low','medium','high'])->default('medium'); // bonus
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
