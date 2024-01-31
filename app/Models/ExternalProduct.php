<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalProduct extends Model
{
    use HasFactory;
    protected $connection = 'ticketsender';

    public function products()
    {
        return $this->belongsToMany(Product::class, 'Products', 'Id');
    }

    public function externalConnection()
    {
        return $this->belongsTo(ExternalConnection::class);
    }
}
