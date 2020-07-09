<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Material extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedDecimal('friction_static')->default(1);
            $table->unsignedDecimal('friction_sliding')->default(0.8);
            $table->unsignedDecimal('friction_rolling')->default(0.01);
            $table->string('three_material_type');
            $table->json('three_material_options');
            // $table->json('material_options')->default(["colour" => "0xd7d7d7", "roughness" => 0.17, "metalness" => 0.47, "reflectivity" => 1, "clearCoat" => 0.64, "clearCoatRoughness" => 0.22]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materials');
    }
}
