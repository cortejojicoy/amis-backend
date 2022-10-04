<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $primaryKey = 'faculty_id';
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
    public function courseOfferings()
    {
        return $this->hasMany(CourseOffering::class, 'id', 'sais_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'sais_id','sais_id');
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'id', 'faculty_id');
    }

        /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeInfo($query)
    {
        // $query->with('user');
        $query->join('users', 'users.sais_id', '=', 'faculties.sais_id');
    }
}
