<?php

namespace Database\Seeders;

use App\Models\GroupMember;
use Illuminate\Database\Seeder;

class GroupMemberSeeder extends Seeder
{
    /**
     * Seed the group_members table with sample team members.
     */
    public function run(): void
    {
        $members = [
            ['name' => 'Andika Maheswara', 'nim' => '20210001', 'class' => 'TI-3A', 'role' => 'Ketua'],
            ['name' => 'Budi Santoso', 'nim' => '20210002', 'class' => 'TI-3A', 'role' => 'Anggota'],
            ['name' => 'Citra Lestari', 'nim' => '20210003', 'class' => 'TI-3A', 'role' => 'Anggota'],
            ['name' => 'Dewi Anggraini', 'nim' => '20210004', 'class' => 'TI-3B', 'role' => 'Anggota'],
            ['name' => 'Eko Prasetyo', 'nim' => '20210005', 'class' => 'TI-3B', 'role' => 'Anggota'],
        ];

        foreach ($members as $member) {
            GroupMember::updateOrCreate(['nim' => $member['nim']], $member);
        }
    }
}
