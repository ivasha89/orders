<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'client_id', 'address', 'deliver_date'
    ];

    public $timestamps = false;

    public function client() {
        return $this->belongsTo(Client::class);
    }
}