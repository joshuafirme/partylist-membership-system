<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private $filePath = 'system_settings.json';

    public function index()
    {
        $settings = json_decode(Storage::get($this->filePath) ?? '{}');
        return view('core.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $settings = json_decode(Storage::get($this->filePath) ?? '{}', true);

        // Update text fields
        $settings['app_name'] = $request->app_name;
        $settings['version'] = $request->version;
        $settings['description'] = $request->description;
        $settings['contact_email'] = $request->contact_email;
        $settings['contact_phone'] = $request->contact_phone;
        $settings['facebook_url'] = $request->facebook_url;
        $settings['twitter_url'] = $request->twitter_url;
        $settings['linkedin_url'] = $request->linkedin_url;

        // Handle File Uploads
        if ($request->hasFile('logo')) {
            $settings['logo_path'] = $request->file('logo')->store('settings', 'public');
        }
        if ($request->hasFile('favicon')) {
            $settings['favicon_path'] = $request->file('favicon')->store('settings', 'public');
        }

        Storage::put($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));

        return back()->with('success', 'System settings updated successfully.');
    }
}