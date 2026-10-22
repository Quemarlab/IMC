<?php
declare(strict_types=1);

namespace YM\Core;

/** Key/value site settings editable from the admin panel, with built-in defaults. */
final class Settings
{
    /** key => [label, default, type] — drives both the public site and the admin form. */
    public const FIELDS = [
        'company_name' => ['Company name', 'Young Masters LTD', 'text'],
        'tagline'      => ['Tagline', 'Mining · OHS · Environment', 'text'],
        'about_short'  => ['Short description (footer & SEO)', 'Young Masters LTD is a Rwanda-registered mining consultancy and training company specializing in Occupational Health and Safety, Environmental Management, Mining Compliance, Geological Services, Mine Planning and Capacity Building.', 'textarea'],
        'phone'        => ['Phone', '+250 783 582 953', 'text'],
        'email'        => ['Public email', 'info@youngmasters.rw', 'email'],
        'address'      => ['Address', 'Kicukiro, Kigali, Rwanda', 'text'],
        'hours'        => ['Working hours', 'Mon – Fri, 08:00 – 17:00', 'text'],
        'whatsapp'     => ['WhatsApp number (digits with country code, e.g. 250783582953)', '', 'text'],
        'linkedin'     => ['LinkedIn URL', '', 'url'],
        'facebook'     => ['Facebook URL', '', 'url'],
        'twitter'      => ['X / Twitter URL', '', 'url'],
        'instagram'    => ['Instagram URL', '', 'url'],
        'youtube'      => ['YouTube URL', '', 'url'],
        'map_embed'    => ['Google Maps embed URL (src of the iframe)', '', 'url'],
    ];

    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            try {
                foreach (DB::all('SELECT skey, value FROM site_settings') as $row) {
                    self::$cache[$row['skey']] = $row['value'];
                }
            } catch (\Throwable) {
                // Table missing (fresh checkout): fall back to defaults.
            }
        }
        return self::$cache;
    }

    public static function get(string $key, string $default = ''): string
    {
        $all = self::all();
        if (isset($all[$key]) && $all[$key] !== '') {
            return $all[$key];
        }
        return self::FIELDS[$key][1] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        DB::run('INSERT INTO site_settings (skey, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)', [$key, $value]);
        self::$cache = null;
    }
}
