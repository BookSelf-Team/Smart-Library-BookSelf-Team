<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'identifier', // NIP / NUPTK / NISN / username
        'role',       // admin | pustakawan | petugas | guru | siswa
        'email',
        'avatar',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function roleEnum(): Role
    {
        return $this->role instanceof Role
            ? $this->role
            : (Role::tryFrom((string) $this->role) ?? Role::Siswa);
    }

    public function hasRole(Role|string ...$roles): bool
    {
        return $this->hasAnyRole($roles);
    }

    /**
     * @param  array<int, Role|string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        $current = $this->roleEnum();

        foreach ($roles as $role) {
            $value = $role instanceof Role ? $role->value : (string) $role;

            if ($current->value === $value) {
                return true;
            }
        }

        return false;
    }

    public function isStaff(): bool
    {
        return $this->roleEnum()->isStaff();
    }

    public function isMember(): bool
    {
        return $this->roleEnum()->isMember();
    }

    public function isAdmin(): bool
    {
        return $this->roleEnum() === Role::Admin;
    }

    public function canManageUsers(): bool
    {
        return $this->roleEnum()->canManageUsers();
    }

    public function canManageSettings(): bool
    {
        return $this->roleEnum()->canManageSettings();
    }

    public function canManageBooks(): bool
    {
        return $this->roleEnum()->canManageBooks();
    }

    public function canCirculate(): bool
    {
        return $this->roleEnum()->canCirculate();
    }

    /**
     * Buat identifier unik otomatis berdasarkan peran, mis. SIS-000012.
     *
     * Nomor urut dilanjutkan dari identifier terbesar dengan prefiks yang sama
     * agar mudah dibaca; tabrakan (mis. akibat pembuatan bersamaan) dilewati.
     */
    public static function generateUniqueIdentifier(Role $role): string
    {
        $prefix = match ($role) {
            Role::Admin => 'ADM',
            Role::Pustakawan => 'PST',
            Role::Petugas => 'PTG',
            Role::Guru => 'GRU',
            Role::Siswa => 'SIS',
            Role::Pengguna => 'USR',
        };

        $latest = static::query()
            ->where('identifier', 'like', $prefix.'-%')
            ->orderByDesc('identifier')
            ->value('identifier');

        $sequence = is_string($latest) ? (int) substr($latest, strlen($prefix) + 1) : 0;

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $sequence++;
            $candidate = sprintf('%s-%06d', $prefix, $sequence);

            if (! static::query()->where('identifier', $candidate)->exists()) {
                return $candidate;
            }
        }

        // Cadangan acak bila nomor urut terus bertabrakan.
        do {
            $candidate = $prefix.'-'.strtoupper(Str::random(8));
        } while (static::query()->where('identifier', $candidate)->exists());

        return $candidate;
    }

    public function roleLabel(): string
    {
        return $this->roleEnum()->label();
    }

    /**
     * Inisial nama (maksimal dua huruf pertama) untuk avatar di antarmuka.
     */
    public function initials(): string
    {
        $parts = preg_split('/\s+/u', trim((string) $this->name)) ?: [];

        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials !== '' ? $initials : '?';
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
