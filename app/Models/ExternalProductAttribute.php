<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalProductAttribute extends Model
{
    use HasFactory;

    protected $connection = 'ticketsender';

    protected $fillable = ['external_product_id', 'name', 'value'];

    public function product()
    {
        return $this->belongsTo(ExternalProduct::class);
    }
}
