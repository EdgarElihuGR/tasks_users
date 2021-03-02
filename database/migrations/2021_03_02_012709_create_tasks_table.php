<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_user_id');
            $table->unsignedBigInteger('assigned_user_id');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('done')->default(0);
            $table->timestamps();

            $table->foreign('creator_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('assigned_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
