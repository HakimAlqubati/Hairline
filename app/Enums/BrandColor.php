<?php

namespace App\Enums;

enum BrandColor: string
{
    case Primary = '#C29E6D';

    /**
     * Get the English name of the color.
     */
    public function nameEn(): string
    {
        return match ($this) {
            self::Primary => 'Primary',
        };
    }

    /**
     * Get the Arabic name of the color.
     */
    public function nameAr(): string
    {
        return match ($this) {
            self::Primary => 'اللون الرئيسي',
        };
    }

    /**
     * Get the localized name based on application locale or specified locale.
     */
    public function label(?string $locale = null): string
    {
        $currentLocale = $locale ?? app()->getLocale();

        return str_starts_with($currentLocale, 'ar') ? $this->nameAr() : $this->nameEn();
    }

    /**
     * Get the lowercase identifier or slug.
     */
    public function slug(): string
    {
        return strtolower($this->name);
    }

    /**
     * Get the HEX color code with hash prefix.
     */
    public function hex(): string
    {
        return $this->value;
    }

    /**
     * Get the RGB components of the color.
     *
     * @return array{r: int, g: int, b: int}
     */
    public function rgb(): array
    {
        $hex = ltrim($this->value, '#');

        return [
            'r' => (int) hexdec(substr($hex, 0, 2)),
            'g' => (int) hexdec(substr($hex, 2, 2)),
            'b' => (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Get the CSS RGB string representation.
     */
    public function rgbString(): string
    {
        $rgb = $this->rgb();

        return "rgb({$rgb['r']}, {$rgb['g']}, {$rgb['b']})";
    }

    /**
     * Get an associative array of color values and their localized labels.
     *
     * @return array<string, string>
     */
    public static function options(?string $locale = null): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label($locale);
        }

        return $options;
    }
}
