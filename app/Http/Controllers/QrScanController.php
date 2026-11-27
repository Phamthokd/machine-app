<?php

namespace App\Http\Controllers;

use App\Support\DeviceDetector;
use Illuminate\Http\Request;

class QrScanController extends Controller
{
    public function index(Request $request)
    {
        $isApp = DeviceDetector::isApp($request);

        return view('scan.index', compact('isApp'));
    }
}
