<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveySetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_sets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('survey_name');
            $table->text('survey_desc')->nullable();
            $table->integer('survey_level')->unsigned()->default(0);
            $table->datetime("start_date")->default("0000-00-00 00:00:00");
            $table->datetime("end_date")->default("0000-00-00 00:00:00");
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
        Schema::dropIfExists('survey_sets');
    }
}
