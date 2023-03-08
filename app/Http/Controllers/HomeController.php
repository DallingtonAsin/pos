<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::check()) {
            return view('pages.home');
        }
        return redirect('/');
    }

    public function overview()
    {
        if (Auth::check()) {
            return view('pages.main.overview');
        }
        return redirect('/');
    }
}
