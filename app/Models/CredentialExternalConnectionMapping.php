<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CredentialExternalConnectionMapping extends Model
{
    use HasFactory;

    protected $connection = 'ticketsender';
    public $timestamps = true;

    public function externalConnection()
    {
        return $this->belongsTo(ExternalConnection::class);
    }

    public function credential()
    {
        return $this->belongsTo(Credential::class, 'credential_id', 'Id');
    }
}
