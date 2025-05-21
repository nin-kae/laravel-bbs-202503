<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function root(): View
    {
        /**
         * Show the application dashboard.
         *
         * @return View
         */
        return view('pages.root');
    }
}
