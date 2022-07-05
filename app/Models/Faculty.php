<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    // /**
    //  * Get the post that owns the comment.
    // */
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

        /**
     * Get the post that owns the comment.
    */
    public function user()
    {
        return $this->belongsTo(User::class,'saisid','saisid');
    }

        /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeBasicInfo($query)
    {
        $query->join('users', 'users.saisid','=','faculties.saisid');
    }
}
