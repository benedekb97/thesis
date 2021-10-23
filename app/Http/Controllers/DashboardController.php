<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.index');
    }
}
