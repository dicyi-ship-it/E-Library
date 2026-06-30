<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function defaults(): array
    {
        return [
            'app_name' => 'E-Library',
            'institution_name' => 'STTI NIIT I-Tech',
            'library_name' => 'Perpustakaan Digital',
            'logo_text' => 'IT',
        ];
    }

    public static function publicValues(): array
    {
        try {
            if (! Schema::hasTable('app_settings')) {
                return self::defaults();
            }

            return array_replace(
                self::defaults(),
                self::query()->pluck('value', 'key')->all()
            );
        } catch (Throwable) {
            return self::defaults();
        }
    }

    public static function getValue(string $key, ?string $fallback = null): string
    {
        $settings = self::publicValues();

        return (string) ($settings[$key] ?? $fallback ?? self::defaults()[$key] ?? '');
    }

    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            self::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
