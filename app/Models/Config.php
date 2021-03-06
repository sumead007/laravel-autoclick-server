<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_login',
        'password',
        'type',
        'status',
        'image_screen_shot',
        'action',
        'queue_num',
        'queue_total',
    ];
}
