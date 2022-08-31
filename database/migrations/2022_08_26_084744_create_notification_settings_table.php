<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('notification_titile',100);
            $table->string('notification_message',255);
            $table->unsignedInteger('customer_group_id')->default(0);
            $table->unsignedInteger('notification_type')->default(0);
            $table->unsignedInteger('notification_times')->default(1);
            $table->unsignedInteger('notification_interval')->default(1);
            $table->unsignedSmallInteger('status')->default(1);
            $table->timestamps();
         

           /* Schema::table($this->notificationsTable(), function (Blueprint $table) {
                $table->foreign('notification_id')
                    ->references('id')
                    ->on($this->table())
                    ->onDelete('cascade')
                ;
            });*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_settings');
    }
}
