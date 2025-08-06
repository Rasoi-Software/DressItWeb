<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'admin_code1',
        'lng',
        'distance',
        'geoname_id',
        'toponym_name',
        'country_id',
        'fcl',
        'population',
        'country_code',
        'name',
        'fcl_name',
        'admin_code_iso',
        'country_name',
        'fcode_name',
        'admin_name1',
        'lat',
        'fcode',
    ];
}
