@props([
    'role',
])

@php
    // Peta warna badge peran agar tiap peran RBAC mudah dibedakan secara visual.
    // Biru untuk Admin; rona lain untuk peran staf dan pemustaka.
    [$pill, $dot] = match ($role) {
        \App\Enums\Role::Admin => ['bg-blue-100 text-blue-800', 'bg-blue-600'],
        \App\Enums\Role::Pustakawan => ['bg-violet-100 text-violet-800', 'bg-violet-600'],
        \App\Enums\Role::Petugas => ['bg-amber-100 text-amber-800', 'bg-amber-500'],
        \App\Enums\Role::Guru => ['bg-teal-100 text-teal-800', 'bg-teal-600'],
        \App\Enums\Role::Siswa => ['bg-rose-100 text-rose-800', 'bg-rose-600'],
        \App\Enums\Role::Pengguna => ['bg-surface-container-highest text-on-surface-variant', 'bg-on-surface-variant'],
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold whitespace-nowrap '.$pill]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}" aria-hidden="true"></span>
    {{ $role->label() }}
</span>
