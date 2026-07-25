<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'site_logo' && $request->hasFile('site_logo')) {
                $path = $request->file('site_logo')->store('settings', 'public');
                Setting::set($key, $path, 'string', 'general');
            } elseif (!is_null($value)) {
                Setting::set($key, $value, 'string', 'general');
            }
        }

        return redirect()->back()->with('success', 'Paramètres généraux mis à jour.');
    }


}
