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
        'OrderProcessingType' => [
            0 => 'None',
            1 => 'FTP',
            2 => 'Rayna',
            3 => 'Rathin'
        ]
    ];

    protected $attributes = [
        'OrderProcessingType' => 0,
        'RaynaTourId' => 0,
        'RaynaTourOptionId' => 0,
        'RaynaContractId' => 0,
        'RaynaAdultPrice' => 0,
        'RaynaChildPrice' => 0,
        'GatePrice' => 0,
        'CostINR' => 0,
        'MinumumSellingPriceAED' => 0
    ];


    public static $orderProcessingTypes = [
        2 => 'Rayna',
        3 => 'Rathin'
    ];
}
