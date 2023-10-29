<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Utilities\RoleUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'role',
        'is_active',
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
        'last_login' =>'datetime'
    ];

    public function hasRole($role):bool
    {
        return is_array($role) ? in_array($this->role,$role) :  $this->role == $role;
    }

    public function candidates()
    {
        $query = Candidate::query();

        if ($this->hasRole(RoleUtility::AGENT)){
            $query->where('owner',$this->id);

        }else if ($this->hasRole(RoleUtility::MANAGER)){
            //we dont have rules for this role
        }

        return $query->get();
    }
}
