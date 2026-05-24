<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'whatsapp_number',
        'message_template',
        'site_mode',
        'launch_date',
        'store_name',
        'store_description',
        'store_logo',
        'store_address',
        'store_email',
        'is_announcement_active',
        'announcement_text',
        'announcement_link',
        'announcement_deadline',
    ];

    protected $casts = [
        'launch_date' => 'datetime',
        'is_announcement_active' => 'boolean',
        'announcement_deadline' => 'date',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::first();
        return $setting ? ($setting->$key ?? $default) : $default;
    }

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'store_name' => 'Cold Storage',
            'whatsapp_number' => '',
            'site_mode' => 'live',
            'message_template' => "Halo, saya ingin memesan:\n\n{items}\n\nTotal: {total}\nPembayaran: {payment}\n\nNama: {name}\nHP: {phone}\n\nCatatan: {note}",
        ]);
    }
}

