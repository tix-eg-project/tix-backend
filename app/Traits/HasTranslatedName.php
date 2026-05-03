<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslatedName
{

    public function getNameAttribute($value)
    {
        $locale = app()->getLocale();
        $decodedValue = json_decode($value, true);

        if (is_array($decodedValue)) {
            // لو المفتاح الحالي موجود
            if (isset($decodedValue[$locale])) {
                return $decodedValue[$locale];
            }

            // fallback للإنجليزي لو موجود
            if (isset($decodedValue['en'])) {
                return $decodedValue['en'];
            }

            // fallback لأول عنصر موجود في الأري
            return reset($decodedValue);
        }

        return $decodedValue;
    }


    public function getDescriptionAttribute($value)
    {
        $locale = app()->getLocale(); // الحصول على اللغة الحالية
        $decodedValue = json_decode($value, true); // فك تشفير الـ JSON إذا كان الحقل مخزنًا كـ JSON

        // التحقق إذا كانت القيمة عبارة عن مصفوفة (أي JSON)
        return is_array($decodedValue) ? $decodedValue[$locale] ?? $decodedValue['en'] : $value;
    }
    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale');

        $key = 'title_' . $locale;
        if (isset($this->$key)) {
            return $this->$key;
        }

        return $this->title_ar ?? null;
    }
    public function getJobTitleAttribute()
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale');

        $key = 'job_title_' . $locale;
        return $this->$key ?? $this->{'job_title_' . $fallback} ?? null;
    }
    public function getAddressAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"address_{$locale}"} ?? $this->address_ar;
    }

    public function getWorkHoursAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"work_hours_{$locale}"} ?? $this->work_hours_ar;
    }
    public function getContentAttribute()
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale');

        $key = 'content_' . $locale;
        return $this->$key ?? $this->{'content_' . $fallback} ?? null;
    }

    public function getTranslation(string $field, string $locale): ?string
    {
        return $this->{$field}[$locale] ?? null;
    }


    /* ========================
 * Mutators: تحويل الإدخال إلى JSON منظّم
 * (للأعمدة: name / unit / group_name / list_options)
 * ====================== */
    public function setNameAttribute($value): void
    {
        $current = $this->decodeJsonField($this->attributes['name'] ?? []);
        $this->attributes['name'] = json_encode(
            $this->normalizeLocaleTextInput($value, $current),
            JSON_UNESCAPED_UNICODE
        );
    }

    private function decodeJsonField($value): array
    {
        if (is_array($value)) return $value;
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) return $decoded;
        }
        return [];
    }

    private function normalizeLocaleTextInput($value, array $current): array
    {
        if (!is_array($value)) {
            $current[app()->getLocale()] = (string) ($value ?? '');
            return $current;
        }
        foreach ($value as $k => $v) {
            if ($v === null) continue;
            $v = is_array($v) ? implode(' ', array_map('strval', $v)) : (string) $v;
            if (trim($v) !== '') $current[(string)$k] = $v;
        }
        return $current;
    }
}
