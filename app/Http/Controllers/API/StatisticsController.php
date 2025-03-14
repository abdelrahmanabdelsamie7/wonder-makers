<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Member, Project , Service , Sponser , contact};

class StatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admins')->only('getStatistics');
    }
    public function getStatistics()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'members_count' => Member::count(),
                'project_count' => Project::count(),
                'services_count' => Service::count(),
                'sponsers_count' => Sponser::count(),
                'contacts_count' => contact::count(),
            ]
        ]);
    }
}
