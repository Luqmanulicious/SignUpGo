<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $connection = 'pgsql';
    protected $keepAlive = false; // Disable connection pooling

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'phone',
        'job_title',
        'organization',
        'certificate_path',
        'address',
        'postcode',
        'website',
        'resume_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the roles associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        try {
            return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
        } catch (\Exception $e) {
            return $this->belongsToMany(Role::class, 'user_roles')->withDefault();
        }
    }

    /**
     * Get the event registrations associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Get the papers submitted by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function papers()
    {
        return $this->hasMany(EventPaper::class);
    }

    /**
     * Get the paper evaluations submitted by the user (as jury).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paperEvaluations()
    {
        return $this->hasMany(PaperEvaluation::class, 'evaluator_id');
    }

    /**
     * Get the paper reviews submitted by the user (as reviewer).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paperReviews()
    {
        return $this->hasMany(PaperReview::class, 'reviewer_id');
    }

    /**
     * Check if user is registered for a specific event.
     *
     * @param int $eventId
     * @param string|null $role
     * @return bool
     */
    public function isRegisteredForEvent($eventId, $role = null)
    {
        $query = $this->eventRegistrations()->where('event_id', $eventId);
        
        if ($role) {
            $query->where('role', $role);
        }
        
        return $query->exists();
    }

    /**
     * Get user's registration for a specific event.
     *
     * @param int $eventId
     * @return EventRegistration|null
     */
    public function getEventRegistration($eventId)
    {
        return $this->eventRegistrations()->where('event_id', $eventId)->first();
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $slug
     * @return bool
     */
    public function hasRole($slug)
    {
        try {
            return $this->roles()->where('slug', $slug)->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Assign a role to the user.
     *
     * @param string|Role $role
     * @return $this
     */
    public function assignRole($role)
    {
        try {
            if (is_string($role)) {
                $role = Role::where('slug', $role)->firstOrFail();
            }
            if (!$this->hasRole($role->slug)) {
                $this->roles()->attach($role->id);
            }
        } catch (\Exception $e) {
            // Silently fail if roles table doesn't exist
        }
        return $this;
    }
}
