<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaptopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laptops', function (Blueprint $table) {
            $table->id();
            $table->foreignID('manufacturer_id');
            $table->foreignID('make_id');
            $table->string('model');
            $table->decimal('price', 8, 2);
            $table->tinyInteger('ram');
            $table->smallInteger('ssd');
            $table->tinyInteger('screen_size');
            $table->enum('default_os', ['Windows', 'MacOS', 'Linux', 'ChromeOS']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');
            $table->foreign('make_id')->references('id')->on('laptop_makes');
        });

        // Add 'MEDUIMBLOB' column (not supported by Eloquent/Schema Create) to store images
        DB::statement('ALTER TABLE laptops ADD image MEDIUMBLOB');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laptops');
    }
}
