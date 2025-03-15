<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * Summary of getJWTIdentifier
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Summary of getJWTCustomClaims
     * @return array
     */
	public function getJWTCustomClaims() { return []; }

    /**
     * Get all categories related to the user with their preference type
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'user_categories')
            ->withPivot('preference_type')
            ->withTimestamps();
    }

    /**
     * Get only preferred categories
     */
    public function preferredCategories()
    {
        // return $this->hasManyThrough(Category::class, UserCategory::class, "category_id", "id", "", "")
        // ->where("preference_type", "=", "preferred");
        return $this->belongsToMany(Category::class, 'user_categories')
            ->wherePivot('preference_type', 'preferred')
            ->withTimestamps();
    }

    /**
     * Get only excluded categories
     */
    public function excludedCategories()
    {
        // return $this->hasManyThrough(Category::class, UserCategory::class, "category_id", "id", "", "")
        // ->where("preference_type", "=", "excluded");
        return $this->belongsToMany(Category::class, 'user_categories')
            ->wherePivot('preference_type', 'excluded')
            ->withTimestamps();
    }
}
