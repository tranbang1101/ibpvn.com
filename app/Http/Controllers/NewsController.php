<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('storefront.news');
    }
}
