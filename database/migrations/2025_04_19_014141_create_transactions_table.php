<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_code');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('member_id')->unsigned()->nullable();
            $table->integer('total_price');
            $table->integer('total_payment');
            $table->integer('change');
            $table->timestamps(0);

            $table->unique('transaction_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
