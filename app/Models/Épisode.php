<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Épisode extends Model
{
     protected $fillable = [
        'titre',
        'description',
        'audio',
        'podcast_id',
    ];
    use HasFactory;
}
