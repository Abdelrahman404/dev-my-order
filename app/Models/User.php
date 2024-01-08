<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'f_name',
        'l_name',
        'phone',
        'email',
        'password',
        'login_medium',
        'ref_by',
        'social_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'interest',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_phone_verified' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'order_count' => 'integer',
        'wallet_balance' => 'float',
        'loyalty_point' => 'integer',
        'ref_by' => 'integer',
    ];


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(){
        return $this->hasMany(CustomerAddress::class);
    }

    public function userinfo()
    {
        return $this->hasOne(UserInfo::class,'user_id', 'id');
    }

    public function scopeZone($query, $zone_id=null){
        $query->when(is_numeric($zone_id), function ($q) use ($zone_id) {
            return $q->where('zone_id', $zone_id);
        });
    }

    public function posts(){

        return $this->hasMany(Post::class);
    }

    public function highlights(){

        return $this->hasMany(Highlight::class);
    }

    public function stories(){

        return $this->hasMany(Story::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    public function followersCount()
    {
        return $this->followers()->count();
    }

    public function followingCount()
    {
        return $this->following()->count();
    }
}
