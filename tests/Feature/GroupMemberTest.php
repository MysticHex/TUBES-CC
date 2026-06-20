<?php

namespace Tests\Feature;

use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupMemberTest extends TestCase
{
    use RefreshDatabase;

    protected function user(): User
    {
        return User::factory()->create();
    }

    public function test_member_list_is_protected(): void
    {
        $this->get(route('members.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_members(): void
    {
        $member = GroupMember::factory()->create();

        $this->actingAs($this->user())
            ->get(route('members.index'))
            ->assertOk()
            ->assertSee($member->name);
    }

    public function test_member_can_be_created(): void
    {
        $this->actingAs($this->user())
            ->post(route('members.store'), [
                'name' => 'New Member',
                'nim' => '12345678',
                'class' => 'TI-3A',
                'role' => 'Anggota',
            ])
            ->assertRedirect(route('members.index'));

        $this->assertDatabaseHas('group_members', ['nim' => '12345678', 'name' => 'New Member']);
    }

    public function test_member_creation_requires_fields(): void
    {
        $this->actingAs($this->user())
            ->post(route('members.store'), [])
            ->assertSessionHasErrors(['name', 'nim', 'class', 'role']);
    }

    public function test_member_can_be_updated(): void
    {
        $member = GroupMember::factory()->create();

        $this->actingAs($this->user())
            ->put(route('members.update', $member), [
                'name' => 'Updated Name',
                'nim' => $member->nim,
                'class' => $member->class,
                'role' => 'Ketua',
            ])
            ->assertRedirect(route('members.index'));

        $this->assertDatabaseHas('group_members', ['id' => $member->id, 'name' => 'Updated Name', 'role' => 'Ketua']);
    }

    public function test_member_can_be_deleted(): void
    {
        $member = GroupMember::factory()->create();

        $this->actingAs($this->user())
            ->delete(route('members.destroy', $member))
            ->assertRedirect(route('members.index'));

        $this->assertDatabaseMissing('group_members', ['id' => $member->id]);
    }
}
