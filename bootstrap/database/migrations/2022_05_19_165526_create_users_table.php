<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->datetime('email_verified_at')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('password');      
            $table->integer('phone_no')->nullable();
            $table->string('gender')->nullable();
            $table->string('location')->nullable();
            $table->string('category_id')->nullable();
            $table->string('status')->nullable();
            $table->string('employment_status')->nullable();
            $table->date('age')->nullable();
            $table->string('social_login_type')->nullable();    
            $table->string('social_login_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
