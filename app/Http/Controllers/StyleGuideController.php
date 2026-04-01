<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StyleGuideController extends Controller
{
    public function index()
    {
        return view('admin.style_guide');
    }
}
