<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Épisode extends Model
{
     use HasFactory;

     protected $fillable = [
        'titre',
        'description',
        'audio',
        'podcast_id',
    ];

    public function podcast(){
        return $this->belongsTo(Podcast::class);
    }
}
