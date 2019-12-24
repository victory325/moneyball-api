<?php

namespace App\Repositories;

use App\Models\Setting;

/**
 * Class SettingRepository
 *
 * @package App\System\Repositories
 * @method Setting find(int $id, array $relations = [])
 */
class SettingRepository extends BaseRepository
{
    /**
     * SettingsRepository constructor.
     *
     * @param Setting $setting
     */
    public function __construct(Setting $setting)
    {
        $this->model = $setting;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return Setting|null
     */
    public function create(string $key, string $value): ?Setting
    {
        $setting = $this->model->newInstance();
        $setting->key = $key;
        $setting->value = $value;

        return $setting->save() ? $setting : null;
    }
}