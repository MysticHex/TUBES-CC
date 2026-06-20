<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupMemberController extends Controller
{
    /**
     * Display a listing of the group members.
     */
    public function index(): View
    {
        $members = GroupMember::latest()->get();

        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new member.
     */
    public function create(): View
    {
        return view('members.create');
    }

    /**
     * Store a newly created member.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMember($request);

        GroupMember::create($validated);

        return redirect()
            ->route('members.index')
            ->with('status', 'Member added successfully.');
    }

    /**
     * Display the specified member.
     */
    public function show(GroupMember $member): View
    {
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(GroupMember $member): View
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified member.
     */
    public function update(Request $request, GroupMember $member): RedirectResponse
    {
        $validated = $this->validateMember($request, $member);

        $member->update($validated);

        return redirect()
            ->route('members.index')
            ->with('status', 'Member updated successfully.');
    }

    /**
     * Remove the specified member.
     */
    public function destroy(GroupMember $member): RedirectResponse
    {
        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('status', 'Member deleted successfully.');
    }

    /**
     * Validate the member payload.
     *
     * @return array<string, mixed>
     */
    protected function validateMember(Request $request, ?GroupMember $member = null): array
    {
        $nimRule = 'unique:group_members,nim';

        if ($member !== null) {
            $nimRule .= ','.$member->id;
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:50', $nimRule],
            'class' => ['required', 'string', 'max:50'],
            'role' => ['required', 'string', 'max:50'],
        ]);
    }
}
