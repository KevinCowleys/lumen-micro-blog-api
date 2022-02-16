<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Block extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'blocked',
        'blocked_by',
    ];

    public function blocked() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'blocked')->select('id', 'name', 'username');
    }
}
