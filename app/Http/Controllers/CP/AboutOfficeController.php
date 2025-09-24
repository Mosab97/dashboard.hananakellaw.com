<?php

namespace App\Http\Controllers\CP;

use App\Http\Controllers\Controller;
use App\Http\Requests\CP\AboutOfficeRequest;
use App\Models\AboutOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Akaunting\Setting\Facade as Setting;

class AboutOfficeController extends Controller
{


    private $config;

    public function __construct()
    {
        $this->config = config('modules.about_office');
    }

    public function index(Request $request)
    {
        return view($this->config['view_path'] . 'index');
    }

    public function addedit(AboutOfficeRequest $request)
    {
        // Site information settings
        if ($request->has('title')) {
            Setting::set('about_office.title', $request->input('title'));
        }
        return redirect()->route($this->config['full_route_name'] . '.index')->with('success', t('About Office updated successfully'));
    }
}
