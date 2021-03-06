<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'like';
    protected $fillable = [
        'user_id', 'activity_id'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function activity()
    {
      return $this->belongsTo(Activity::class);
    }
}
