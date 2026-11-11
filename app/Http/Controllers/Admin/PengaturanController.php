<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage-settings');

        return view('admin.pengaturan.index', [
            'dendaPerHari' => Setting::dendaPerHari(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('manage-settings');

        $validated = $request->validate([
            'denda_per_hari' => ['required', 'integer', 'min:0', 'max:1000000'],
        ]);

        Setting::setDendaPerHari($validated['denda_per_hari']);

        return back()->with('success', 'Tarif denda per hari berhasil disimpan: Rp '
            .number_format($validated['denda_per_hari'], 0, ',', '.').'.');
    }
}
