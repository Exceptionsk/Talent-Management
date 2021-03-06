<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'course';
    protected $fillable = [
        'name', 'image', 'start_date', 'end_date', 'descriptions'
    ];

    protected $hidden = [
       'image', 'created_at', 'deleted_at', 'updated_at'
    ];

    public function details()
    {
      return $this->hasMany(Topic::class);
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }
}
