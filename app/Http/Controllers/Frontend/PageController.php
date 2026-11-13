<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function howItWorks(): View
    {
        return view('public.pages.how-it-works');
    }

    public function terms(): View
    {
        return view('public.pages.terms');
    }

    public function privacy(): View
    {
        return view('public.pages.privacy');
    }
}
