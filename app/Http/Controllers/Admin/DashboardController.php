<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\House;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'total' => House::count(),

            'draft' => House::where('status', 'draft')
                ->count(),

            'submitted' => House::where('status', 'submitted')
                ->count(),

            'verified' => House::where('status', 'verified')
                ->count(),

            'published' => House::where('status', 'published')
                ->count(),

            'revision' => House::where('status', 'revision')
                ->count(),

            'public' => House::where('is_public', true)
                ->count(),
        ]);
    }
}   