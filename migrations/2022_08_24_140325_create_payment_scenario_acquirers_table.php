<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreatePaymentScenarioAcquirersTable extends Migration
{
    public function up(): void
    {
        Schema::create('payment_scenario_acquirers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('payment_scenario_id');
            $table->unsignedInteger('acquirer_id');
            $table->integer('priority')->index();

            $table->foreign('payment_scenario_id')
                ->references('id')->on('payment_scenarios')
                ->onDelete('cascade');

            $table->foreign('acquirer_id')
                ->references('id')->on('acquirers');

            $table->unique(['payment_scenario_id', 'acquirer_id'], 'unique_payment_scenario_acquirer');
        });
    }

    public function down(): void
    {
        Schema::table('payment_scenario_acquirers', function (Blueprint $table) {
            $table->drop();
        });
    }
}