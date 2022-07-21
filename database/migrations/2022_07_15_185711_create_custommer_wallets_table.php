<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustommerWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       /* Schema::create('custommer_wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->double('credit');
            $table->double('debit');
            $table->integer('payment_status')->default(0);
            $table->integer('order_id');
            $table->integer('offer_id');
            $table->string('transaction_id',50);
            $table->string('transaction_type',50);
            $table->string('remark',100);
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custommer_wallets');
    }
}
