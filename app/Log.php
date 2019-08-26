<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'log';
    protected $fillable = [
        'admin_id', 'action', 'category', 'descriptions'
    ];
}