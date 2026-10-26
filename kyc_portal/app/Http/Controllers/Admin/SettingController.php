<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'groups' => config('portal.settings', []),
            'settings' => Setting::query()->get()->keyBy('key'),
        ]);
    }

    public function update(Request $request, SettingService $settings): RedirectResponse
    {
        $rules = [];

        foreach (config('portal.settings', []) as $group) {
            foreach ($group['fields'] as $key => $field) {
                $rules['settings.'.self::safeKey($key)] = $field['rules'] ?? ['nullable', 'string'];
            }
        }

        $validated = $request->validate($rules);

        $values = [];
        foreach ($validated['settings'] ?? [] as $safeKey => $value) {
            $values[str_replace('__', '.', $safeKey)] = $value;
        }

        $settings->update($values, $request->user());

        return back()->with('status', 'Settings saved.');
    }

    public static function safeKey(string $key): string
    {
        return str_replace('.', '__', $key);
    }
}
