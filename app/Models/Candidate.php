<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'source'];

    public function owner()
    {
         return $this->belongsTo(User::class,'owner','id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
