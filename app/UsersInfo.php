<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersInfo extends Model
{

    protected $table='users_info';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','first_name','last_name',
    ];

}