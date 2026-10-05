<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\House;

class DashboardController extends Controller
{
    public function index()
    {
        $query = House::query();

        if (!auth()->user()->hasRole('admin')) {
            $query->where(
                'created_by',
                auth()->id()
            );
        }

        return view('surveyor.dashboard', [
            'mine' => (clone $query)->count(),

            'draft' => (clone $query)
                ->where('status', 'draft')
                ->count(),

            'submitted' => (clone $query)
                ->where('status', 'submitted')
                ->count(),

            'revision' => (clone $query)
                ->where('status', 'revision')
                ->count(),

            'verified' => (clone $query)
                ->where('status', 'verified')
                ->count(),

            'published' => (clone $query)
                ->where('status', 'published')
                ->count(),
        ]);
    }
}