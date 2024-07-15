<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApiLog extends Model
{
    use HasFactory;
    protected $connection = 'ticketsender';
    public $timestamps = false;

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }
}
