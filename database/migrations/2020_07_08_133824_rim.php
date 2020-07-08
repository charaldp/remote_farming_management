<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rim extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tires', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tire_type')->default('flat');
            $table->json('type_dimensions')->default(["DO" => 0.5,"DI" => 0.43,"t" => 0.15]);
            $table->sting('material_id');
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
