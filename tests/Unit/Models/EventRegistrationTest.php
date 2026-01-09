<?php

namespace Tests\Unit\Models;

use App\Models\EventRegistration;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    /** @test */
    public function it_has_correct_casts()
    {
        $reg = new EventRegistration();
        $casts = $reg->getCasts();

        $this->assertEquals('datetime', $casts['registered_at']);
        $this->assertEquals('array', $casts['jury_categories']);
    }

    /** @test */
    public function it_determines_status_flags_correctly()
    {
        $reg = new EventRegistration();
        
        $reg->status = 'pending';
        $this->assertTrue($reg->is_pending);
        $this->assertFalse($reg->is_approved);
        
        $reg->status = 'approved';
        $this->assertTrue($reg->is_approved);
        $this->assertFalse($reg->is_pending);

        $reg->status = 'confirmed'; // also counts as approved
        $this->assertTrue($reg->is_approved);

        $reg->status = 'rejected';
        $this->assertTrue($reg->is_rejected);
    }

    /** @test */
    public function it_determines_role_flags_correctly()
    {
        $reg = new EventRegistration();
        
        $reg->role = 'jury';
        $this->assertTrue($reg->is_jury);
        
        $reg->role = 'participant';
        $this->assertTrue($reg->is_participant);
        
        $reg->role = 'reviewer';
        $this->assertTrue($reg->is_reviewer);
    }

    /** @test */
    public function it_returns_correct_badge_colors()
    {
        $reg = new EventRegistration();
        
        $reg->status = 'approved';
        $this->assertEquals('success', $reg->status_badge_color);

        $reg->status = 'pending';
        $this->assertEquals('warning', $reg->status_badge_color);

        $reg->role = 'jury';
        $this->assertEquals('purple', $reg->role_badge_color);
        
        $reg->role = 'participant';
        $this->assertEquals('green', $reg->role_badge_color);
    }
}
