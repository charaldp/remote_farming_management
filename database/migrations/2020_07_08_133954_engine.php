<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Engine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedDecimal('shaft_inertia')->default(50);
            $table->unsignedDecimal('rev_limit')->default(116.66); // rps
            $table->unsignedDecimal('idle_rot')->default(17.5); // rps
            $table->unsignedDecimal('maximum_hp')->default(390);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
