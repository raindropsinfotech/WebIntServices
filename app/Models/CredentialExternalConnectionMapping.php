<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CredentialExternalConnectionMapping extends Model
{
    use HasFactory;

    protected $connection = 'ticketsender';
    public $timestamps = true;
}
