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

    /**
     * URL sampul buku untuk ditampilkan di antarmuka.
     * Mendukung path penyimpanan relatif (disk public) maupun URL absolut.
     */
    public function coverUrl(): ?string
    {
        if (blank($this->cover_image)) {
            return null;
        }

        if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
            return $this->cover_image;
        }

        return asset('storage/'.$this->cover_image);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
