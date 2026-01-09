<?php

namespace Tests\Feature\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventPaper;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock CloudinaryService
        $this->mock(CloudinaryService::class, function ($mock) {
            $mock->shouldReceive('uploadPoster')->andReturn(['secure_url' => 'http://example.com/poster.jpg']);
            $mock->shouldReceive('deleteByUrl')->andReturn(true);
        });
    }

    /** @test */
    public function guests_cannot_access_registration_page()
    {
        $event = Event::factory()->create();
        $response = $this->get(route('registrations.create', $event));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_users_can_view_registration_page()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'status' => 'published',
            'start_date' => now()->addDays(10),
            'registration_deadline' => now()->addDays(5),
            'current_participants' => 0,
            'max_participants' => 100,
        ]);

        $response = $this->actingAs($user)
            ->get(route('registrations.create', $event));

        $response->assertStatus(200);
        $response->assertViewIs('registrations.create');
    }

    /** @test */
    public function cannot_view_registration_page_for_closed_validation()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'status' => 'draft', // Not published
        ]);

        $response = $this->actingAs($user)
            ->get(route('registrations.create', $event));

        $response->assertRedirect(route('events.show', $event));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function participant_can_register_successfully()
    {
        $user = User::factory()->create();
        // Assume factory sets default category/organizer
        $event = Event::factory()->create([
            'status' => 'published',
            'start_date' => now()->addDays(10),
            'registration_deadline' => now()->addDays(5),
            'max_participants' => 100,
            'current_participants' => 0,
            'delivery_mode' => 'face_to_face',
            'is_public' => true,
        ]);

        $paperFile = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

        $response = $this->actingAs($user)->post(route('registrations.store', $event), [
            'role' => 'participant',
            'paper_title' => 'My Research Paper',
            'paper_abstract' => 'This is an abstract.',
            'paper_theme' => 'Technology',
            'paper_poster' => $paperFile,
        ]);

        $response->assertRedirect(route('registrations.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('event_registrations', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'role' => 'participant',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('event_papers', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'title' => 'My Research Paper',
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function jury_can_register_successfully()
    {
        $user = User::factory()->create(['certificate_path' => 'certs/cert.pdf']);
        $event = Event::factory()->create([
            'status' => 'published',
            'start_date' => now()->addDays(10),
            'delivery_mode' => 'online',
        ]);

        $response = $this->actingAs($user)->post(route('registrations.store', $event), [
            'role' => 'jury',
            'jury_themes' => ['AI', 'Blockchain'],
            // jury_categories might be optional depending on event setup, assumed optional here or handled by test data setup
            'jury_categories' => ['Tech'],
        ]);

        $response->assertRedirect(route('registrations.index'));

        $this->assertDatabaseHas('event_registrations', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'role' => 'jury',
        ]);
    }

    /** @test */
    public function cannot_register_twice_for_same_role()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['status' => 'published']);
        
        EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'role' => 'participant',
            'status' => 'pending',
            'registration_code' => 'TEST-REG',
        ]);

        $response = $this->actingAs($user)->post(route('registrations.store', $event), [
            'role' => 'participant',
            // ... other fields irrelevant as check happens early
        ]);

        $response->assertRedirect(); // Should redirect back
        $response->assertSessionHas('info'); // "Already registered" uses info flash
    }

    /** @test */
    public function user_can_view_their_registrations()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        
        EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'role' => 'participant',
            'status' => 'pending',
            'registration_code' => 'TEST-123',
        ]);

        $response = $this->actingAs($user)->get(route('registrations.index'));
        
        $response->assertStatus(200);
        $response->assertSee($event->title);
        $response->assertSee('Participant');
    }

    /** @test */
    public function user_can_edit_pending_participant_registration()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['status' => 'published']);
        
        $registration = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'role' => 'participant',
            'status' => 'pending',
            'registration_code' => 'TEST-EDIT',
        ]);

        $paper = EventPaper::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'title' => 'Old Title',
            'abstract' => 'Old Abstract',
            'status' => 'draft',
            'paper_theme' => 'Old Theme'
        ]);

        $response = $this->actingAs($user)->put(route('registrations.update', $registration), [
            'paper_title' => 'New Title',
            'paper_abstract' => 'New Abstract',
            'paper_theme' => 'New Theme',
        ]);

        $response->assertRedirect(route('registrations.index'));
        $this->assertDatabaseHas('event_papers', [
            'id' => $paper->id,
            'title' => 'New Title',
        ]);
    }

    /** @test */
    public function cannot_edit_approved_registration()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        
        $registration = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'role' => 'participant',
            'status' => 'approved', // Cannot edit approved
            'registration_code' => 'TEST-APPROVED',
        ]);
        
        // Ensure paper exists to pass unrelated checks
        EventPaper::create([
             'user_id' => $user->id,
             'event_id' => $event->id,
             'title' => 'Title', 
             'abstract' => 'Abstract', 
             'status' => 'draft',
             'paper_theme' => 'Theme'
        ]);

        $response = $this->actingAs($user)->put(route('registrations.update', $registration), [
            'paper_title' => 'New Title',
        ]);

        $response->assertRedirect(route('registrations.index'));
        $response->assertSessionHas('error', 'This registration cannot be edited.');
    }

    /** @test */
    public function user_can_cancel_registration()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        
        $registration = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'role' => 'participant',
            'status' => 'pending',
            'registration_code' => 'TEST-CANCEL',
        ]);

        $response = $this->actingAs($user)->delete(route('registrations.destroy', $registration));

        $response->assertRedirect(route('registrations.index'));
        $this->assertDatabaseMissing('event_registrations', [
            'id' => $registration->id,
        ]);
    }
}
