<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateAcquirersTable extends Migration
{
    public function up(): void
    {
        Schema::create('acquirers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->integer('default')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('acquirers', function (Blueprint $table) {
            $table->drop();
        });
    }
}