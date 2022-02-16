<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'muted',
        'muted_by',
    ];

    public function muted() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'muted')->select('id', 'name', 'username');
    }
}
