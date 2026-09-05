<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    protected $fillable = ['user_id', 'company_id', 'original_url', 'short_code'];

    // withTrashed() is used because users have soft deletes
    // This ensures URLs still show the creator's name even after the user is deleted
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
