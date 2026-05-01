<?php

namespace App\Services;

use App\Models\ExtraSetting;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function sharedViewData(): array
    {
        return Cache::rememberForever('shared_view_data', function (): array {
            return [
                'setting' => Setting::query()->find(1),
                'extra_settings' => ExtraSetting::query()->find(1),
                'menus' => Menu::query()->find(1),
            ];
        });
    }

    public function setting(): ?Setting
    {
        return $this->sharedViewData()['setting'];
    }

    public function clear(): void
    {
        Cache::forget('shared_view_data');
    }
}
