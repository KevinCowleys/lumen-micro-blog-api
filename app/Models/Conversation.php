<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
    ];

    public function sender() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'sender_id')->select('id', 'name', 'username');
    }

    public function recipient() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'recipient_id')->select('id', 'name', 'username');
    }

    public function messages() : HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id', 'id')->with('user');
    }
}
