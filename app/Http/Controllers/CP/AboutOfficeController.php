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
use Exception;

class AboutOfficeController extends Controller
{


    private $config;

    public function __construct()
    {
        $this->config = config('modules.about_office');
    }

    public function index(Request $request)
    {
        // dd(session('success'));
        return view($this->config['view_path'] . 'index');
    }

    public function addedit(AboutOfficeRequest $request)
    {
        try {
            // Site information settings
            if ($request->has('title')) {
                Setting::set('about_office.title', $request->input('title'));
            }
            // dd($request->all(), Setting::get('about_office.title'));
            if ($request->has('description')) {
                Setting::set('about_office.description', $request->input('description'));
            }
            if ($request->has('features')) {
                Setting::set('about_office.features', $request->input('features'));
            }
            if ($request->file('image')) {
                $imagePath = Storage::disk('public')->putFile('about_office', $request->file('image'));
                Setting::set('about_office.image', $imagePath);
            }

            // Save all settings
            Setting::save();

            DB::commit();
            return redirect()->back()->with('success', t('Settings updated successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', t('Error updating settings: ') . $e->getMessage());
        }
    }
}
