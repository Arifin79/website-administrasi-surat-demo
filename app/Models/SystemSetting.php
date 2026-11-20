<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::firstWhere('setting_key', $key)?->setting_value ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        self::updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
    }
}
