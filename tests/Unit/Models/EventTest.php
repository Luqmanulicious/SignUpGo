<?php

namespace Tests\Unit\Models;

use App\Models\Event;
use Carbon\Carbon;
use Tests\TestCase;

class EventTest extends TestCase
{
    /** @test */
    public function it_has_correct_casts()
    {
        $event = new Event();
        $casts = $event->getCasts();

        $this->assertEquals('datetime', $casts['start_date']);
        $this->assertEquals('boolean', $casts['is_free']);
        $this->assertEquals('array', $casts['tags']);
    }

    /** @test */
    public function it_handles_boolean_accessors_for_postgres_compatibility()
    {
        $event = new Event();
        
        // Test getter
        $this->assertTrue($event->getIsFreeAttribute('true'));
        $this->assertTrue($event->getIsFreeAttribute('t'));
        $this->assertTrue($event->getIsFreeAttribute('1'));
        $this->assertTrue($event->getIsFreeAttribute(true));
        $this->assertFalse($event->getIsFreeAttribute('false'));
        $this->assertFalse($event->getIsFreeAttribute(false));
    }

    /** @test */
    public function it_generates_poster_url_correctly()
    {
        $event = new Event();
        
        // Null case
        $event->featured_image = null;
        $this->assertNull($event->poster_url);

        // Full URL case
        $event->featured_image = 'http://example.com/image.jpg';
        $this->assertEquals('http://example.com/image.jpg', $event->poster_url);

        // Local path case (mocking env is tricky here without helper, assuming default behavior)
        $event->featured_image = 'images/poster.jpg';
        $this->assertStringContainsString('storage/images/poster.jpg', $event->poster_url);
    }

    /** @test */
    public function it_identifies_event_status_correctly()
    {
        $event = new Event();
        
        // Upcoming
        $event->start_date = Carbon::tomorrow();
        $this->assertTrue($event->is_upcoming);

        // Past
        $event->end_date = Carbon::yesterday();
        $this->assertTrue($event->is_past);

        // Active
        $event->start_date = Carbon::yesterday();
        $event->end_date = Carbon::tomorrow();
        $this->assertTrue($event->is_active);
    }

    /** @test */
    public function it_checks_if_user_can_register()
    {
        $event = new Event();
        $event->status = 'draft';
        $this->assertFalse($event->can_register, 'Should not register if not published');

        $event->status = 'published';
        $event->registration_deadline = Carbon::yesterday();
        $this->assertFalse($event->can_register, 'Should not register if deadline passed');

        $event->registration_deadline = Carbon::tomorrow();
        $event->max_participants = 10;
        $event->current_participants = 10;
        $event->allow_waitlist = false;
        $this->assertFalse($event->can_register, 'Should not register if full and no waitlist');

        $event->allow_waitlist = true;
        $this->assertTrue($event->can_register, 'Should register if full but waitlist allowed');
    }

    /** @test */
    public function it_calculates_available_slots()
    {
        $event = new Event();
        $event->max_participants = 100;
        $event->current_participants = 20;
        
        $this->assertEquals(80, $event->available_slots);

        $event->current_participants = 120; // Overbooked
        $this->assertEquals(0, $event->available_slots);
        
        $event->max_participants = null; // Unlimited
        $this->assertEquals(PHP_INT_MAX, $event->available_slots);
    }
}
