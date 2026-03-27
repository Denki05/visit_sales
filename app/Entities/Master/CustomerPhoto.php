<?php

namespace App\Entities\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerPhoto extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_other_address_id',
        'file',
        'type',
        'gps_latitude',
        'gps_longitude',
        'taken_at',
        'created_by'
    ];
}
