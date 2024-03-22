<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
    protected $connection = 'ticketsender';
    protected $table = 'Orders';
    protected $primaryKey = 'Id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ShopOrderNumber',
        'status',
        'PaymentStatus',
        'CustomerName',
        'CustomerEmail'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'OrderId', 'Id');
    }

    public function getRouteKeyName()
    {
        return 'Id'; // Replace 'id' with your route key name if different
    }

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


    /**
     * The attributes that are enum.
     *
     * @var array
     */
    protected $enums = [
        'Status' => [
            0 => 'New',
            1 => 'PartiallyCompleted',
            2 => 'Completed',
            3 => 'Cancelled'
        ],
        'PaymentStatus' => [
            0 => 'Unpaid',
            1 => 'PartiallyPaid',
            2 => 'Paid',
            3 => 'Refunded'
        ],
    ];

    public function externalConnection()
    {
        return $this->belongsTo(ExternalConnection::class, 'external_connection_id', 'id');
    }

    protected $attributes = [
        'Status' => 0,
        'PaymentStatus' => 0,
        'ShopSystem' => 0 // in next version this will be obsolete as we will use external_connection
    ];

    //Casts of the model dates
    protected $casts = [
        'OrderDateTime' => 'date'
    ];
}
