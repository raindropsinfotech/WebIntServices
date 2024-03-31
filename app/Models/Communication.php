<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Communication extends Model
{
    use HasFactory;
    protected $connection = 'ticketsender';

    function communicable(): MorphTo
    {
        return $this->morphTo();
    }
}
