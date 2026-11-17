<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Storage;

class BusinessSettingController extends Controller
{
    public function edit()
    {
        $settings = BusinessSetting::current();
        return view('settings.business', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:50'],
            'alternate_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'receipt_footer' => ['nullable', 'string'],
            'terms_and_conditions' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
        ]);

        $settings = BusinessSetting::current();

        if ($request->hasFile('logo')) {
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('business', 'public');
        }

        $settings->update($validated);

        return redirect()->route('settings.business')->with('success', 'Business profile settings updated successfully.');
    }
}
