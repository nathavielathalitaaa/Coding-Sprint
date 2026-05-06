<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSetting extends Model
{
    protected $table = 'document_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Retrieve a setting value by key.
     */
    public static function get(string $key, mixed $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Upsert a setting.
     */
    public static function set(string $key, mixed $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
