<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreatePaymentScenariosTable extends Migration
{
    public function up(): void
    {
        Schema::create('payment_scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 10)->index('payment_scenarios_brand_index');
            $table->integer('installment_interval_start')->index();
            $table->integer('installment_interval_end')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('payment_scenarios', function (Blueprint $table) {
            $table->drop();
        });
    }
}