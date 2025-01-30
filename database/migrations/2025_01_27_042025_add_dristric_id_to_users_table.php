<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDristricIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('dristric_id')->unsigned()->nullable();
            $table->foreign('dristric_id')->references('dr_id')->on('dristric')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->dropForeign(['dristric_id']);
            // $table->dropColumn('dristric_id');
        });
    }
}
