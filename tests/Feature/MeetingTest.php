<?php

namespace Tests\Feature;

use App\Models\Meeting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_loads(): void
    {
        $this->get(route('meetings.index'))->assertStatus(200);
    }

    public function test_create_page_loads(): void
    {
        $this->get(route('meetings.create'))->assertStatus(200);
    }

    public function test_can_schedule_a_meeting(): void
    {
        $this->post(route('meetings.store'), [
            'date'  => '2026-06-01',
            'time'  => '10:00',
            'title' => 'Intro to Laravel',
            'type'  => '1-on-1',
        ])->assertRedirect(route('meetings.index'));

        $this->assertDatabaseHas('meetings', [
            'title'  => 'Intro to Laravel',
            'status' => 'scheduled',
        ]);
    }

    public function test_meeting_requires_title_date_and_time(): void
    {
        $this->post(route('meetings.store'), [])->assertSessionHasErrors(['date', 'time', 'title']);
    }

    public function test_can_add_notes_after_meeting(): void
    {
        $meeting = Meeting::create([
            'date'  => '2026-06-01',
            'time'  => '10:00',
            'title' => 'Intro to Laravel',
            'type'  => '1-on-1',
        ]);

        $this->put(route('meetings.update', $meeting), [
            'topics'      => 'Routes, Controllers, Blade',
            'accomplished'=> 'Built a CRUD app',
            'action_items'=> 'Practice migrations',
            'notes'       => 'Great session!',
        ])->assertRedirect(route('meetings.show', $meeting));

        $this->assertDatabaseHas('meetings', [
            'id'     => $meeting->id,
            'status' => 'completed',
            'topics' => 'Routes, Controllers, Blade',
        ]);
    }

    public function test_can_delete_a_meeting(): void
    {
        $meeting = Meeting::create([
            'date'  => '2026-06-01',
            'time'  => '10:00',
            'title' => 'To be deleted',
            'type'  => '1-on-1',
        ]);

        $this->delete(route('meetings.destroy', $meeting))
             ->assertRedirect(route('meetings.index'));

        $this->assertDatabaseMissing('meetings', ['id' => $meeting->id]);
    }
}
