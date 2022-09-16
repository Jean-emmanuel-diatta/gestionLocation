<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payements', function (Blueprint $table) {
            $table->id();
            $table->double("montantPaye");
            $table->dateTime('datePayement');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('location_id')->constrained();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payements',function (Blueprint $table){
            $table->dropForeign(["user_id","location_id"]);
        });
        Schema::dropIfExists('payements');
    }
}
