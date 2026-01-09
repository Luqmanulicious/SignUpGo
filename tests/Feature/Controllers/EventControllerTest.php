<?php

namespace Tests\Feature\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_published_events()
    {
        Event::factory()->create(['title' => 'Published Event', 'status' => 'published']);
        Event::factory()->create(['title' => 'Draft Event', 'status' => 'draft']);

        $response = $this->get(route('events.index'));

        $response->assertStatus(200);
        $response->assertSee('Published Event');
        $response->assertDontSee('Draft Event');
    }

    /** @test */
    public function it_can_filter_events_by_search_term()
    {
        Event::factory()->create(['title' => 'Laravel Conference', 'status' => 'published']);
        Event::factory()->create(['title' => 'React Meetup', 'status' => 'published']);

        $response = $this->get(route('events.index', ['search' => 'Laravel']));

        $response->assertSee('Laravel Conference');
        $response->assertDontSee('React Meetup');
    }

    /** @test */
    public function it_can_filter_events_by_category()
    {
        $cat1 = EventCategory::create(['name' => 'Tech', 'slug' => 'tech']);
        $cat2 = EventCategory::create(['name' => 'Art', 'slug' => 'art']);

        Event::factory()->create(['title' => 'Tech Talk', 'category_id' => $cat1->id, 'status' => 'published']);
        Event::factory()->create(['title' => 'Art Show', 'category_id' => $cat2->id, 'status' => 'published']);

        $response = $this->get(route('events.index', ['category' => $cat1->id]));

        $response->assertSee('Tech Talk');
        $response->assertDontSee('Art Show');
    }

    /** @test */
    public function it_can_filter_events_by_date_range()
    {
        Event::factory()->create([
            'title' => 'Future Event',
            'start_date' => now()->addDays(10),
            'status' => 'published'
        ]);
        Event::factory()->create([
            'title' => 'Past Event',
            'start_date' => now()->subDays(10),
            'status' => 'published'
        ]);

        $response = $this->get(route('events.index', [
            'date_from' => now()->addDays(1)->format('Y-m-d')
        ]));

        $response->assertSee('Future Event');
        $response->assertDontSee('Past Event');
    }

    /** @test */
    public function it_can_filter_events_by_type()
    {
        Event::factory()->create(['title' => 'Free Event', 'is_free' => true, 'status' => 'published']);
        Event::factory()->create(['title' => 'Paid Event', 'is_free' => false, 'status' => 'published']);

        // Test Free
        $response = $this->get(route('events.index', ['type' => 'free']));
        $response->assertSee('Free Event');
        $response->assertDontSee('Paid Event');

        // Test Paid
        $response = $this->get(route('events.index', ['type' => 'paid']));
        $response->assertSee('Paid Event');
        $response->assertDontSee('Free Event');
    }

    /** @test */
    public function it_shows_event_details()
    {
        $event = Event::factory()->create(['title' => 'My Event', 'description' => 'Detailed description', 'status' => 'published']);

        $response = $this->get(route('events.show', $event));

        $response->assertStatus(200);
        $response->assertSee('My Event');
        $response->assertSee('Detailed description');
    }
}
