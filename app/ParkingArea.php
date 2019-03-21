<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParkingArea extends Model
{

    protected $table='parking_area';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','longitude','latitude','space','space_left','free_time','weekday_first','weekday','weekend_first','weekend',
    ];

}