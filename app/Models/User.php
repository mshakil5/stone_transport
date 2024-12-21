<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password', 
        'is_type',
        'phone',
        'address',
        'nid',
        'nid_image',
        'house_number',
        'street_name',
        'town',
        'postcode',
        'country',
        'photo',
        'about',
        'facebook',
        'twitter',
        'google',
        'linkedin',
        'whatsapp',
        'status',
        'updated_by',
        'created_by',
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

    protected function type(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ["0", "1", "2"][$value],
        );
    }

    public function customerTransaction()
    {
        return $this->hasMany(Transaction::class, 'customer_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(Order::class);
    }

}
