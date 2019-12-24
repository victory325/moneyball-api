<?php

namespace App\Services;

use App\Repositories\SettingRepository;

/**
 * Class SettingService
 * @package App\Services
 */
class SettingService
{
    protected $settingRepository;

    /**
     * SettingService constructor.
     */
    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Publishes settings to application config
     */
    public function load(): void
    {
        $rows = $this->settingRepository->all();

        foreach ($rows as $row) {
            config()->set('settings.' . $row->key, $row->value);
        }
    }
}