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

if (!function_exists('route_admin_email_for_testing')) {
    function route_admin_email_for_testing($email)
    {
        $email = trim((string) $email);
        $targetEmail = 'admin@varnihomes.com.au';
        $testSuffix = '.test.-google-a.com';

        if (strcasecmp($email, $targetEmail) === 0) {
            return $targetEmail . $testSuffix;
        }

        return $email;
    }
}

