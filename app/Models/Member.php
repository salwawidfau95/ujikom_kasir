<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'no_phone',
        'point',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
