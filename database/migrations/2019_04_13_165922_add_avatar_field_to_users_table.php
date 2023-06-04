<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvatarFieldToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users', function (Blueprint $table){
            $table->string('avatar', 200)->nullable();
            $table->integer('parent')->default(0);
            $table->string('lang', 10)->default('en');
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'users', function (Blueprint $table){
            $table->dropColumn(
                [
                    'avatar',
                    'parent',
                ]
            );
        }
        );
    }
}
