<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParkingCode extends Model
{

    protected $table='parking_code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parking_id','code',
    ];

}