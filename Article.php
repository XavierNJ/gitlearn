<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use Notifiable;

    protected $table = 'articles';

    protected $fillable = [
        'title', 'content',
    ];
}