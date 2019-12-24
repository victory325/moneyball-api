<?php

use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class AddRafflePrizeRowToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = Carbon::now();

        DB::table('settings')->insert([
            [
                'key'        => 'raffle_prize',
                'value'      => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
