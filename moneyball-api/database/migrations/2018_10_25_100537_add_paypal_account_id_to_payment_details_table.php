<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaypalAccountIdToPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->string('paypal_account_id')->nullable();
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->string('paypal_batch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn('paypal_account_id');
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->dropColumn('paypal_batch_id');
        });
    }
}
