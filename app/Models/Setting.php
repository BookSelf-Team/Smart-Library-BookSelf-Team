<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const KEY_DENDA_PER_HARI = 'denda_per_hari';

    /** Tabel key-value memakai kolom "key" sebagai primary key string. */
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value'];

    /**
     * Tarif denda keterlambatan per hari (Rupiah).
     * Default 0 berarti denda belum diaktifkan oleh admin.
     */
    public static function dendaPerHari(): int
    {
        return (int) static::query()->where('key', self::KEY_DENDA_PER_HARI)->value('value');
    }

    public static function setDendaPerHari(int $nominal): void
    {
        static::query()->updateOrCreate(
            ['key' => self::KEY_DENDA_PER_HARI],
            ['value' => (string) max(0, $nominal)],
        );
    }
}
