<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friends extends Model
{
    protected $fillable = [
        'user_one', 'user_two', 'accepted','rejected',
    ];



}
