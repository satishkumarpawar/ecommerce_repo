<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSurveyQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_survey_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cate_id')->unsigned();
            $table->text('question_text')->nullable();
            $table->integer('question_order')->unsigned()->default(0);
            $table->boolean('question_lock')->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();
            $table->foreign('cate_id')->references('id')->on('survey_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_survey_questions');
    }
}
