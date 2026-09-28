<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeamLeaderMemberVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private UserRole $teamLeaderRole;

    private UserRole $memberRole;

    private Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teamLeaderRole = UserRole::where('name', 'Team Leader')->firstOrFail();
        $this->memberRole = UserRole::create([
            'name' => 'Member',
            'permissions' => [],
        ]);
        $this->team = Team::create([
            'name' => 'Test Team',
            'status' => true,
        ]);
    }

    public function test_migration_creates_a_user_with_the_team_leader_role(): void
    {
        $leader = User::where('email', 'teamleader@portal.com')->firstOrFail();

        $this->assertSame('Team Leader', $leader->role->name);
        $this->assertContains('view_members', $leader->role->permissions);
    }

    public function test_team_leader_only_sees_members_assigned_to_them(): void
    {
        [$leader, $assignedMember, $otherMember] = $this->createLeaderAndMembers();

        $response = $this->actingAs($leader)->get(route('members.index'));

        $response->assertOk();
        $response->assertViewHas('members', function ($members) use ($assignedMember, $otherMember) {
            $ids = $members->getCollection()->modelKeys();

            return in_array($assignedMember->id, $ids, true)
                && ! in_array($otherMember->id, $ids, true);
        });
    }

    public function test_search_cannot_expose_another_leaders_member(): void
    {
        [$leader, $assignedMember, $otherMember] = $this->createLeaderAndMembers();

        $response = $this->actingAs($leader)->get(route('members.index', [
            'search' => $otherMember->membership_number,
        ]));

        $response->assertOk();
        $response->assertViewHas('members', function ($members) use ($assignedMember, $otherMember) {
            $ids = $members->getCollection()->modelKeys();

            return ! in_array($assignedMember->id, $ids, true)
                && ! in_array($otherMember->id, $ids, true);
        });
    }

    public function test_team_leader_cannot_open_an_unassigned_members_eid(): void
    {
        [$leader, , $otherMember] = $this->createLeaderAndMembers();

        $this->actingAs($leader)
            ->get(route('members.eid', $otherMember))
            ->assertForbidden();
    }

    public function test_team_leader_export_only_contains_assigned_members(): void
    {
        [$leader, $assignedMember, $otherMember] = $this->createLeaderAndMembers();

        $content = $this->actingAs($leader)
            ->get(route('members.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString($assignedMember->membership_number, $content);
        $this->assertStringNotContainsString($otherMember->membership_number, $content);
    }

    public function test_team_leader_can_add_and_edit_an_assigned_member_with_a_profile_photo(): void
    {
        Storage::fake('public');
        $leader = User::factory()->create([
            'password' => 'password',
            'user_role_id' => $this->teamLeaderRole->id,
            'team_id' => $this->team->id,
        ]);

        $this->actingAs($leader)
            ->post(route('members.store'), $this->memberPayload([
                'name' => 'New Assigned Member',
                'profile_photo' => UploadedFile::fake()->image('new-member.jpg'),
            ]))
            ->assertRedirect(route('members.index'));

        $member = User::where('name', 'New Assigned Member')->firstOrFail();
        $this->assertSame($leader->id, $member->coordinator_id);
        $this->assertSame($leader->team_id, $member->team_id);
        Storage::disk('public')->assertExists($member->profile_photo_path);
        $oldPhotoPath = $member->profile_photo_path;

        $this->actingAs($leader)
            ->put(route('members.update', $member), $this->memberPayload([
                'name' => 'Updated Assigned Member',
                'profile_photo' => UploadedFile::fake()->image('updated-member.png'),
            ]))
            ->assertRedirect(route('members.index'));

        $member->refresh();
        $this->assertSame('Updated Assigned Member', $member->name);
        Storage::disk('public')->assertMissing($oldPhotoPath);
        Storage::disk('public')->assertExists($member->profile_photo_path);
    }

    public function test_admin_can_upload_a_members_profile_photo_and_it_appears_on_the_eid(): void
    {
        Storage::fake('public');
        $adminRole = UserRole::create([
            'name' => 'Super Admin',
            'permissions' => ['all'],
        ]);
        $admin = User::factory()->create([
            'password' => 'password',
            'user_role_id' => $adminRole->id,
        ]);
        [, $member] = $this->createLeaderAndMembers();

        $this->actingAs($admin)
            ->put(route('members.update', $member), $this->memberPayload([
                'name' => $member->name,
                'profile_photo' => UploadedFile::fake()->image('admin-upload.jpg'),
            ]))
            ->assertRedirect(route('members.index'));

        $member->refresh();
        Storage::disk('public')->assertExists($member->profile_photo_path);

        $this->actingAs($admin)
            ->get(route('members.index'))
            ->assertOk()
            ->assertSee(
                'data-photo="'.asset('storage/'.$member->profile_photo_path).'"',
                false
            );

        $this->actingAs($admin)
            ->get(route('members.eid', $member))
            ->assertOk()
            ->assertSee('storage/'.$member->profile_photo_path, false);
    }

    private function createLeaderAndMembers(): array
    {
        $leader = User::factory()->create([
            'password' => 'password',
            'user_role_id' => $this->teamLeaderRole->id,
            'team_id' => $this->team->id,
        ]);
        $otherLeader = User::factory()->create([
            'password' => 'password',
            'user_role_id' => $this->teamLeaderRole->id,
            'team_id' => $this->team->id,
        ]);
        $assignedMember = User::factory()->create([
            'name' => 'Assigned Member',
            'password' => 'password',
            'user_role_id' => $this->memberRole->id,
            'team_id' => $this->team->id,
            'coordinator_id' => $leader->id,
            'membership_number' => 'PL-TEST-ASSIGNED',
            'qr_token' => 'assigned-member-qr-token',
        ]);
        $otherMember = User::factory()->create([
            'name' => 'Other Member',
            'password' => 'password',
            'user_role_id' => $this->memberRole->id,
            'team_id' => $this->team->id,
            'coordinator_id' => $otherLeader->id,
            'membership_number' => 'PL-TEST-OTHER',
            'qr_token' => 'other-member-qr-token',
        ]);

        return [$leader, $assignedMember, $otherMember];
    }

    private function memberPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Member',
            'user_role_id' => $this->memberRole->id,
            'voter_status' => 'registered',
            'status' => 1,
        ], $overrides);
    }
}
