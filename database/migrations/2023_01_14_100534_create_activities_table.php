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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->string('activity_budget_tw1');
            $table->string('activity_budget_tw2');
            $table->string('activity_budget_tw3');
            $table->string('activity_budget_tw4');

            $table->string('activity_realized_tw1')->nullable();
            $table->string('activity_realized_tw2')->nullable();
            $table->string('activity_realized_tw3')->nullable();
            $table->string('activity_realized_tw4')->nullable();

            $table->integer('document_plan_tw1');
            $table->integer('document_plan_tw2');
            $table->integer('document_plan_tw3');
            $table->integer('document_plan_tw4');

            $table->integer('document_realized_tw1')->nullable();
            $table->integer('document_realized_tw2')->nullable();
            $table->integer('document_realized_tw3')->nullable();
            $table->integer('document_realized_tw4')->nullable();

            $table->bigInteger('program_id')->unsigned();

            $table->softDeletes();
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
        Schema::dropIfExists('activities');
    }
};
