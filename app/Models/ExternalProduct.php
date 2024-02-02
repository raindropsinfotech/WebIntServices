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
        return $this->belongsToMany(Product::class, 'product_external_product', 'external_product_id', 'product_id');
    }

    public function externalConnection()
    {
        return $this->belongsTo(ExternalConnection::class);
    }
}
