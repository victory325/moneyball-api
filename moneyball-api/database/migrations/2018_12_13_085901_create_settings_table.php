<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->string('value');
            $table->timestamps();
        });

        $now = Carbon::now();

        DB::table('settings')->insert([
            [
                'key'        => 'raffle_prize_min',
                'value'      => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'raffle_prize_max',
                'value'      => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'raffle_prize_currency',
                'value'      => 'USD',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'raffle_min_level',
                'value'      => 6,
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
        Schema::dropIfExists('settings');
    }
}
