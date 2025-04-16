<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function presentation()
    {
        return view('pages.presentation');
    }

    public function motDirecteur()
    {
        return view('pages.mot-directeur');
    }

    public function recrutement()
    {
        return view('pages.recrutement');
    }

    public function calendrier()
    {
        return view('pages.calendrier');
    }

    public function administration()
    {
        return view('pages.administration');
    }
} 