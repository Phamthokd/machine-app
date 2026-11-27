<?php

namespace App\Support;

use Illuminate\Http\Request;

class DeviceDetector
{
    /**
     * Determine if current request is coming from the native Android WebView App (PDA or tablet/phone).
     */
    public static function isApp(?Request $request = null): bool
    {
        $request = $request ?? request();

        // Allow manual query override for testing (?app=1 or ?web=1 or ?app=0)
        if ($request->has('app')) {
            return $request->boolean('app') || $request->query('app') === '1';
        }
        if ($request->has('web')) {
            return !($request->boolean('web') || $request->query('web') === '1');
        }

        $ua = $request->userAgent() ?? '';

        // App-specific identifiers configured in Android WebView
        if (str_contains($ua, 'VivaApp') || str_contains($ua, 'MachineApp')) {
            return true;
        }

        // Standard Android WebView tokens: "; wv" or "Version/... Chrome/... Mobile"
        if (str_contains($ua, '; wv') || str_contains($ua, '; wv;')) {
            return true;
        }

        // Android WebView automatic request header
        $requestedWith = $request->header('X-Requested-With') ?? '';
        if (str_contains($requestedWith, 'com.vivagarment.machineapp') || str_contains($requestedWith, 'machineapp')) {
            return true;
        }

        return false;
    }
}
