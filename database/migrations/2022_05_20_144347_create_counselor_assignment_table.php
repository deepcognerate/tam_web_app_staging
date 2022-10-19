<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCounselorAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counselor_assignment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('counselor_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->string('chat_type');
            $table->enum('availability',array(0,1))->default(0);
            $table->foreign('user_id',)->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id',)->references('id')->on('category')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counselor_assignment');
    }
}
