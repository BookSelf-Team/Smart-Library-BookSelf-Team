<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public const STATUS_ACTIVE = 'ACTIVE';

    public const STATUS_COMPLETED = 'COMPLETED';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'book_id',
        'tanggal_pinjam',
        'batas_kembali',
        'tanggal_dikembalikan',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'batas_kembali' => 'date',
        'tanggal_dikembalikan' => 'date',
    ];

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereNull('tanggal_dikembalikan');
    }

    #[Scope]
    protected function overdue(Builder $query): void
    {
        $query->active()->whereDate('batas_kembali', '<', today());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }
}
