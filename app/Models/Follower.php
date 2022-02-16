<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Follower extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'following',
        'follower',
    ];

    public function following() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'following')->select('id', 'name', 'username');
    }

    public function follower() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'follower')->select('id', 'name', 'username');
    }
}
