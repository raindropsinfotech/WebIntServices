<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'ticketsender';
    protected $table = 'Products';
    protected $primaryKey = 'Id';

    public $timestamps = true;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'CreatedAt'; // Replace with your uppercase column name

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'UpdatedAt'; // Replace with your uppercase column name


    public function externalProducts()
    {
        return $this->belongsToMany(ExternalProduct::class, 'product_external_product', 'product_id', 'external_product_id');
    }

    /**
     * The attributes that are enum.
     *
     * @var array
     */
    protected $enums = [
        'ProductType' => [
            0 => 'Single',
            1 => 'Combo'
        ],
    ];
}
