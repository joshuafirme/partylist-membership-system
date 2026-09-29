<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModulePermissionVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_with_view_events_permission_cannot_see_or_use_event_management_actions(): void
    {
        $member = $this->userWithPermissions('Member', ['view_events']);
        $event = Event::create([
            'title' => 'Visible Event',
            'event_date' => now()->addDay(),
            'created_by' => $member->id,
            'status' => true,
        ]);

        $this->actingAs($member)
            ->get(route('events.index'))
            ->assertOk()
            ->assertSee('Visible Event')
            ->assertDontSee('Create Event')
            ->assertDontSee('title="Edit"', false)
            ->assertDontSee('title="Delete"', false)
            ->assertDontSee('id="eventModal"', false);

        $this->actingAs($member)
            ->post(route('events.store'), $this->eventPayload())
            ->assertForbidden();
        $this->actingAs($member)
            ->put(route('events.update', $event), $this->eventPayload())
            ->assertForbidden();
        $this->actingAs($member)
            ->delete(route('events.destroy', $event))
            ->assertForbidden();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Visible Event',
        ]);
    }

    public function test_view_only_permissions_hide_management_controls_in_each_list_module(): void
    {
        $viewer = $this->userWithPermissions('Read Only', [
            'view_events',
            'view_teams',
            'view_attendances',
        ]);

        $this->actingAs($viewer)
            ->get(route('events.index'))
            ->assertOk()
            ->assertDontSee('Create Event')
            ->assertDontSee('Actions');

        $this->actingAs($viewer)
            ->get(route('teams.index'))
            ->assertOk()
            ->assertDontSee('Add New Team')
            ->assertDontSee('Actions')
            ->assertDontSee('id="teamModal"', false);

        $this->actingAs($viewer)
            ->get(route('attendances.index'))
            ->assertOk()
            ->assertDontSee('data-module="Manual Check-In"', false)
            ->assertDontSee('Actions')
            ->assertDontSee('id="overrideModal"', false);
    }

    public function test_manage_permission_implies_access_to_the_corresponding_list_module(): void
    {
        $eventManager = $this->userWithPermissions('Event Manager', ['manage_events']);
        $teamManager = $this->userWithPermissions('Team Manager', ['manage_teams']);
        $attendanceManager = $this->userWithPermissions('Attendance Manager', ['manage_attendances']);

        $this->actingAs($eventManager)
            ->get(route('events.index'))
            ->assertOk()
            ->assertSee('Create Event');

        $this->actingAs($teamManager)
            ->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Add New Team');

        $this->actingAs($attendanceManager)
            ->get(route('attendances.index'))
            ->assertOk()
            ->assertSee('Manual Override');
    }

    public function test_dashboard_only_renders_modules_the_user_can_view(): void
    {
        $member = $this->userWithPermissions('Member', ['view_events']);

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Active Events')
            ->assertDontSee('Total Teams')
            ->assertDontSee('Total Attendances')
            ->assertDontSee('View Complete Log');
    }

    private function userWithPermissions(string $roleName, array $permissions): User
    {
        $role = UserRole::create([
            'name' => $roleName,
            'permissions' => $permissions,
        ]);

        return User::factory()->create([
            'password' => 'password',
            'user_role_id' => $role->id,
        ]);
    }

    private function eventPayload(): array
    {
        return [
            'title' => 'Unauthorized Event',
            'event_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            'status' => 1,
        ];
    }
}
