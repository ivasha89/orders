<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = [
        'name', 'cost', 'days'
    ];

    public $timestamps = false;
}