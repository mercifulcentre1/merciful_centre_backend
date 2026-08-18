<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('setting_value', 'setting_key');
        return response()->json(['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
        }
        return response()->json(['message' => 'Settings updated successfully']);
    }
}
