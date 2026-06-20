<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the group profile page.
     */
    public function show(): View
    {
        return view('profile', [
            'groupName' => 'Cloud Computing Group',
            'course' => 'Cloud Computing',
            'members' => GroupMember::orderBy('name')->get(),
        ]);
    }
}
