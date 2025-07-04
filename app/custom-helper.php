<?php

if (!function_exists('asset_url')) {
    function asset_url($path = '')
    {
        // Assuming your assets are directly in the public directory
        return asset($path);
    }
}
if (!function_exists('get_setting')) {
    /**
     * Retrieve a setting value by its key for the authenticated user.
     *
     * @param string $key
     * @return string|null
     */
    function get_setting($key)

    {
        $userId = auth()->id();
        $setting = \App\Models\Setting::where('setting_key', $key)
            ->where('user_id', $userId)
            ->first();

        return $setting ? $setting->setting_value : null;
    }
    
}

