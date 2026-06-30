<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('settings.edit', [
            'settings' => AppSetting::publicValues(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'institution_name' => ['required', 'string', 'max:150'],
            'library_name' => ['required', 'string', 'max:150'],
            'logo_text' => ['required', 'string', 'max:6'],
        ]);

        AppSetting::putMany($data);

        return redirect()->route('settings.edit')->with('status', 'Pengaturan aplikasi berhasil disimpan.');
    }
}
