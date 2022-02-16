<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content',
        'conversation_id',
        'user_id',
    ];

    public function user() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select('id', 'name', 'username');
    }
}
