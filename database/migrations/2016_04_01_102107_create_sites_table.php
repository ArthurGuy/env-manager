<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->longText('env');
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->timestamp('accessed_at')->nullable();
            $table->integer('viewed_by')->nullable();
            $table->integer('edited_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sites');
    }
}
