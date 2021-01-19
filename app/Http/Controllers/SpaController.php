<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpaController extends Controller
{
    public function index()
    {
        return view('spa');
    }
    public function hi(){
        return view('shows.shows');
    }
}
