<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Car extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('engine_id');
            $table->unsignedBigInteger('wheel_id');
            $table->unsignedBigInteger('body_material_id');
            $table->string('name');
            $table->string('creation_type')->default('points2D');
            $table->multiPolygon('body_points');
            // ->default(new Polygon([new LineString([
            //         new Point(1.7, 0),
            //         new Point(1.68, 0.05),
            //         new Point(1.67, 0.19),
            //         new Point(1.7, 0.25),
            //         new Point(1.69, 0.32),
            //         new Point(1.67, 0.34),
            //         new Point(0.55, 0.47),
            //         new Point(0.1, 0.65),
            //         new Point(-0.7, 0.67),
            //         new Point(-1.3,  0.4),
            //         new Point(-1.79, 0.41),
            //         new Point(-1.8, 0.4),
            //         new Point(-1.8, 0.09),
            //         new Point(-1.85, 0.09),
            //         new Point(-1.85, 0)
            //     ])
            // ]));
            $table->multiPolygon('wheel_center_positions');
            // ->default(new Polygon([new LineString([
            //     new Point(-1, 0.8), new Point(1, 0.8) ])]));
            $table->unsignedDecimal('radius')->default(0.27);
            $table->unsignedDecimal('width')->default(1.9);
            $table->unsignedDecimal('bevel_thickness')->default(0.05);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
