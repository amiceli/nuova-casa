<?php

namespace App\Models;

use App\Enums\Theme;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = array(
        'name',
        'email',
        'github_id',
        'name',
        'avatar',
        'github_token',
        'github_refresh_token',
        'theme',
        'onboarding_completed_at',
    );

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = array(
        'password',
        'remember_token',
    );

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return array(
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'theme' => Theme::class,
            'onboarding_completed_at' => 'datetime',
        );
    }

    /**
     * A user goes through the onboarding once, right after their first login.
     */
    public function hasCompletedOnboarding(): bool {
        return $this->onboarding_completed_at !== null;
    }

    public function completeOnboarding(): void {
        $this->onboarding_completed_at = now();

        $this->save();
    }

    public function pages() {
        return $this->hasMany(Page::class);
    }

    public function tags() {
        return $this->hasMany(Tag::class);
    }

    public function newsletters() {
        return $this->hasMany(Newsletter::class);
    }
}
