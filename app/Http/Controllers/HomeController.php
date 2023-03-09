<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Customer;


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
    public function index(Request $request)
    {

        if (Auth::check()) {
            $customers = Customer::select(['id', 'name'])->get();
            return view('pages.home')->with(compact('customers'));
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
