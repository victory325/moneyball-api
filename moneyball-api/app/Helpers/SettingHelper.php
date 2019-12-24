<?php

namespace App\Helpers;

/**
 * Class SettingHelper
 * @package App\Helpers
 */
class SettingHelper
{
    /**
     * @return null|string
     */
    public static function rafflePrizeMin(): ?string
    {
        return self::get('raffle_prize_min');
    }

    /**
     * @return null|string
     */
    public static function rafflePrizeMax(): ?string
    {
        return self::get('raffle_prize_max');
    }

    /**
     * @return null|string
     */
    public static function rafflePrize(): ?string
    {
        return self::get('raffle_prize');
    }

    /**
     * @return null|string
     */
    public static function raffleCurrency(): ?string
    {
        return self::get('raffle_prize_currency');
    }

    /**
     * @return null|string
     */
    public static function raffleMinLevel(): ?string
    {
        return self::get('raffle_min_level');
    }

    /**
     * Returns setting by name
     *
     * @param string $name
     *
     * @return null|string
     */
    public static function get(string $name): ?string
    {
        return config('settings.' . $name);
    }
}
