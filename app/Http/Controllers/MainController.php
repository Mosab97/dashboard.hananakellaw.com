<?php

namespace App\Http\Controllers;

// use App\Models\RoleModel;

class MainController extends Controller
{
    public function index()
    {
        $data = [];

        return view('website.index', $data);
    }
}
