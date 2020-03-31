<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * mokeup function @isAuthorized can be used for dealing
     * with sensitive data. Can be utilized when, for ex. using
     * user roles/levels... for now it is set to return only true
     * for the functions that will be require it in the future development.
     *
     * @return bool
     */
    public static function isAuthorized(){
        // if $this->role = 'admin', return true...
        return true;
    }

}
