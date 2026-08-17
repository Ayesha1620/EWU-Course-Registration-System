<?php

namespace App\Controllers;

use App\Controller;

class HealthController extends Controller
{
    // GET /api/health — API আর DB connection ঠিক আছে কিনা check
    public function index(): void
    {
        $this->db->query('SELECT 1');
        json_success([
            'db'   => 'connected',
            'time' => date('Y-m-d H:i:s'),
        ], 'API is running');
    }
}