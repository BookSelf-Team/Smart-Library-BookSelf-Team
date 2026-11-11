@extends('layouts.admin')

@section('title', 'Kelola Pengguna — Bookself')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col gap-6">
    <x-breadcrumb :items="[['label' => 'Kelola Pengguna']]" />
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Pengaturan</span>
            <h1 class="font-headline-md text-headline-md text-on-surface font-semibold">Kelola Pengguna</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Kelola akun, peran, dan hak akses seluruh pengguna sistem perpustakaan</p>
        </div>
        <button onclick="openModal('createModal')" type="button" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary/90 shadow-[0_2px_8px_-2px_rgba(35,45,19,0.25)] transition-colors">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            <span class="font-title-sm text-title-sm">Tambah Pengguna</span>
        </button>
    </div>

    @if (session('success'))
        <div class="bg-primary-container text-on-primary-container px-4 py-3 rounded-lg font-body-md shadow-sm flash-in">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-error-container text-on-error-container px-4 py-3 rounded-lg font-body-md shadow-sm flash-in">{{ session('error') }}</div>
    @endif

    <div class="bg-surface-container-low shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-surface-container-highest">
                <thead class="bg-surface-container">
                    <tr>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Pengguna</th>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Email</th>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Role</th>
                        <th class="px-4 py-3 text-right font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-surface-container-highest">
                    @forelse ($users as $u)
                        @php($canUpdate = auth()->user()->can('update', $u))
                        @php($canDelete = auth()->user()->can('delete', $u))
                        <tr class="hover:bg-surface-container-low/60">
                            <td class="px-4 py-3 min-w-[180px]">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-surface-container-high text-on-surface-variant flex items-center justify-center font-title-sm text-title-sm font-semibold shrink-0" aria-hidden="true">
                                        {{ $u->initials() }}
                                    </div>
                                    <div class="min-w-0 flex flex-col">
                                        <span class="font-title-sm text-title-sm text-on-surface font-semibold truncate">{{ $u->name }}</span>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $u->identifier ?? 'Tanpa identifier' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-body-md text-on-surface-variant whitespace-nowrap">{{ $u->email }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-role-badge :role="$u->roleEnum()" />
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($canUpdate)
                                        <button type="button"
                                                data-id="{{ $u->id }}"
                                                data-name="{{ $u->name }}"
                                                data-identifier="{{ $u->identifier }}"
                                                data-email="{{ $u->email }}"
                                                data-role="{{ $u->role }}"
                                                onclick="openEditUserFromBtn(this)"
                                                class="p-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors"
                                                title="Ubah data & role pengguna"
                                                aria-label="Edit pengguna {{ $u->name }}">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                    @endif
                                    @if ($canDelete)
                                        <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" onsubmit="return confirm('Yakin hapus pengguna ini? Aksesnya ke sistem perpustakaan akan dicabut permanen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/40 transition-colors" title="Hapus pengguna" aria-label="Hapus pengguna {{ $u->name }}">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                    @if (! $canUpdate && ! $canDelete)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-surface-container text-on-surface-variant font-label-sm text-label-sm"
                                              title="Akun ini dilindungi kebijakan sistem (admin terakhir / akun sendiri)">
                                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">lock</span>
                                            Dilindungi
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center font-body-md text-on-surface-variant">Belum ada pengguna</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="px-4 py-3 border-t border-surface-container-highest">{{ $users->links() }}</div>
        @endif
    </div>
</div>

<div id="createModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-lg bg-surface rounded-lg shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-surface-container-highest flex items-center justify-between shrink-0">
            <h2 class="font-title-lg text-title-lg text-on-surface">Tambah Pengguna</h2>
            <button onclick="closeModal('createModal')" class="p-2 rounded-lg hover:bg-surface-container"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" class="p-4 sm:p-6 flex flex-col gap-4 overflow-y-auto">
            @csrf
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-on-surface">Nama</label>
                <input name="name" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Identifier <span class="font-body-sm text-on-surface-variant">(opsional)</span></label>
                    <input name="identifier" placeholder="Kosongkan untuk dibuat otomatis" class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Email</label>
                    <input name="email" type="email" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Role</label>
                    <select name="role" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach (\App\Enums\Role::assignable() as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Password</label>
                    <input name="password" type="password" required minlength="6" class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary/90">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-lg bg-surface rounded-lg shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-surface-container-highest flex items-center justify-between shrink-0">
            <h2 class="font-title-lg text-title-lg text-on-surface">Edit Pengguna</h2>
            <button onclick="closeModal('editModal')" class="p-2 rounded-lg hover:bg-surface-container"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form id="editForm" method="POST" class="p-4 sm:p-6 flex flex-col gap-4 overflow-y-auto">
            @csrf
            @method('PUT')
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-on-surface">Nama</label>
                <input name="name" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Identifier <span class="font-body-sm text-on-surface-variant">(opsional)</span></label>
                    <input name="identifier" placeholder="Kosongkan untuk memakai identifier saat ini" class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Email</label>
                    <input name="email" type="email" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Role</label>
                    <select name="role" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                        @foreach (\App\Enums\Role::assignable() as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Password (kosongkan jika tidak diubah)</label>
                    <input name="password" type="password" minlength="6" class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Biarkan kosong">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary/90">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal(id){ const m=document.getElementById(id); if(m){ m.classList.remove('hidden'); m.classList.add('flex'); document.body.classList.add('overflow-hidden'); } }
function closeModal(id){ const m=document.getElementById(id); if(m){ m.classList.add('hidden'); m.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); } }
function openEditUserFromBtn(btn){
    const d = btn.dataset;
    openEdit(d.id, d);
}
function openEdit(id, u){
    const form=document.getElementById('editForm');
    form.action='{{ url('admin/users') }}/'+id;
    form.querySelector('[name=name]').value=u.name||'';
    form.querySelector('[name=identifier]').value=u.identifier||'';
    form.querySelector('[name=email]').value=u.email||'';
    form.querySelector('[name=role]').value=u.role||'siswa';
    form.querySelector('[name=password]').value='';
    openModal('editModal');
}
</script>
@endpush
@endsection
