<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with summary cards and server status.
     */
    public function index(): View
    {
        return view('dashboard', [
            'totalMembers' => GroupMember::count(),
            'databaseStatus' => $this->databaseStatus(),
        ]);
    }

    /**
     * Determine whether the MySQL database connection is reachable.
     */
    protected function databaseStatus(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
