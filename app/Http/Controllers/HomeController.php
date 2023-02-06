<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;

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
        if(Auth::check())
        {
        return view('pages.home');
        }else{
            return redirect('/');
        }
    }

    public function overview()
    {
        if(Auth::check())
        {
            return view('pages.main.overview');
            }else{
                return redirect('/');
            }
       
    }
    
}
