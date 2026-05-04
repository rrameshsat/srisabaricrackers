<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\UpiConfig;

class UpiConfigController extends Controller
{
    public function index()
    {
        $config = UpiConfig::first();
        return view('upi.index', ['config' => $config]);
    }

    public function store(Request $request)
    {
        $data = $request->only(['enabled', 'merchant_id', 'endpoint']);
        $data['enabled'] = $request->has('enabled');
        $config = UpiConfig::first();
        if (!$config) {
            $config = UpiConfig::create($data);
        } else {
            $config->update($data);
        }
        return redirect()->back()->with('status', 'UPI config saved');
    }
}
