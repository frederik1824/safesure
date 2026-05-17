<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TraspasoController extends Controller
{
    /**
     * Display the Traspasos Dashboard.
     */
    public function index()
    {
        return view('admin.traspasos.index');
    }
}
