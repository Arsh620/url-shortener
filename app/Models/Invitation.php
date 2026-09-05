<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = ['email', 'role', 'company_id', 'token', 'invited_by', 'expires_at'];
}
