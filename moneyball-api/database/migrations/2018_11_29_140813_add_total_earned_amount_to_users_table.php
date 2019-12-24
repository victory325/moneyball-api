<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalEarnedAmountToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('total_earned_amount')->default(0)->after('available_amount');
        });

        $totals = DB::table('transfers')
            ->select(['user_id', DB::raw('SUM(amount) as total')])
            ->groupBy('user_id')
            ->get();

        foreach ($totals as $total) {
            DB::table('users')
                ->where(['id' => $total->user_id])
                ->update(['total_earned_amount' => $total->total]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_earned_amount');
        });
    }
}
