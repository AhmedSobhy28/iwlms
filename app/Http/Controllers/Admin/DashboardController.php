<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(): Response
    {
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'categories' => Category::query()->count(),
                'courses' => Course::query()->count(),
            ],
        ]);
    }
}
