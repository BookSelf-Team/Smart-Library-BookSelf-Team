<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_buku',
        'judul',
        'pengarang',
        'tahun_terbit',
        'jumlah_stok',
        'sinopsis',
        'cover_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun_terbit' => 'integer',
        'jumlah_stok' => 'integer',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
