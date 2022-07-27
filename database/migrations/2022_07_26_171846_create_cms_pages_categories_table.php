<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsPagesCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_pages_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cate_name',100);
            $table->text('cate_desc')->nullable();
            $table->integer('cate_order')->default(0);
            $table->boolean('status')->default(0);
            $table->boolean('restrict')->default(0);
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
        Schema::dropIfExists('cms_pages_categories');
    }
}
