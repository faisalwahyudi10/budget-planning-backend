<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->bigInteger('activity_budget_tw1')->change();
            $table->bigInteger('activity_budget_tw2')->change();
            $table->bigInteger('activity_budget_tw3')->change();
            $table->bigInteger('activity_budget_tw4')->change();

            $table->bigInteger('activity_realized_tw1')->change();
            $table->bigInteger('activity_realized_tw2')->change();
            $table->bigInteger('activity_realized_tw3')->change();
            $table->bigInteger('activity_realized_tw4')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            //
        });
    }
};
