<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public function creator_user(){
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function assigned_user(){
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
