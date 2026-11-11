<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Pustakawan = 'pustakawan';
    case Petugas = 'petugas';
    case Guru = 'guru';
    case Siswa = 'siswa';

    /**
     * Peran lama yang masih mungkin tersimpan di database.
     */
    case Pengguna = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Pustakawan => 'Pustakawan Ahli',
            self::Petugas => 'Petugas Sirkulasi',
            self::Guru => 'Dewan Guru',
            self::Siswa => 'Siswa / Pemustaka',
            self::Pengguna => 'Pengguna',
        };
    }

    public function isStaff(): bool
    {
        return in_array($this, [self::Admin, self::Pustakawan, self::Petugas], true);
    }

    public function isMember(): bool
    {
        return in_array($this, [self::Siswa, self::Guru, self::Pengguna], true);
    }

    public function canManageUsers(): bool
    {
        return $this === self::Admin;
    }

    public function canManageSettings(): bool
    {
        return $this === self::Admin;
    }

    public function canManageBooks(): bool
    {
        return in_array($this, [self::Admin, self::Pustakawan], true);
    }

    public function canCirculate(): bool
    {
        return in_array($this, [self::Admin, self::Pustakawan, self::Petugas], true);
    }

    public function canViewReports(): bool
    {
        return $this->canCirculate();
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin, self::Pustakawan => 'admin.dashboard',
            self::Petugas => 'petugas.dashboard',
            self::Guru, self::Siswa, self::Pengguna => 'siswa.dashboard',
        };
    }

    /**
     * Peran yang boleh dipilih saat membuat/mengubah akun.
     *
     * @return array<int, self>
     */
    public static function assignable(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $role): bool => $role !== self::Pengguna,
        ));
    }

    /**
     * @return array<int, string>
     */
    public static function assignableValues(): array
    {
        return array_map(fn (self $role): string => $role->value, self::assignable());
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role): string => $role->value, self::cases());
    }

    /**
     * Peran yang boleh meminjam buku (anggota/pemustaka).
     *
     * @return array<int, string>
     */
    public static function memberValues(): array
    {
        return array_values(array_map(
            fn (self $role): string => $role->value,
            array_filter(self::cases(), fn (self $role): bool => $role->isMember()),
        ));
    }
}
