<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustomFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'custom_fields', function (Blueprint $table){
            $table->boolean('is_required')->default('1')->after('status');
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
            'custom_fields', function (Blueprint $table){
            $table->dropColumn(
                [
                    'is_required',
                ]
            );
        }
        );
    }
}
