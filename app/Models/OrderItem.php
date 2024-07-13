<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;

class OrderItem extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;
    protected $connection = 'ticketsender';
    protected $table = 'OrderItems';
    public $timestamps = true;

    protected $primaryKey = 'Id';

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ServiceDateTime' => 'datetime', // Adjust the column name as needed
        'ProcessDateTime' => 'datetime',
    ];

    protected $attributes = [
        'CheckStatus' => 0,
        'Availibility' => 0,
        'BookingStatus' => 0,
        'DeliveryStatus' => 0,
        'RaynaBookingId' => 0,
        'Children' => 0,
        'IsProcessed' => 0
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductId', 'Id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderId', 'Id');
    }

    public function communications(): MorphMany
    {
        return $this->morphMany(Communication::class, 'communicable');
    }

    // Define a mutator for IsProcessed attribute
    public function setIsProcessedAttribute($value)
    {
        $this->attributes['IsProcessed'] = $value;

        // Automatically update OrderItemStatus based on conditions
        $this->updateOrderItemStatus();
    }

    // Define a mutator for PostpondDelivery attribute
    public function setPostpondDeliveryAttribute($value)
    {
        $this->attributes['PostpondDelivery'] = $value;

        // Automatically update OrderItemStatus based on conditions
        $this->updateOrderItemStatus();
    }

    // Method to update OrderItemStatus attribute based on conditions
    protected function updateOrderItemStatus()
    {
        if ($this->IsProcessed) {
            $this->OrderItemStatus = 2;
        } elseif (!$this->IsProcessed && $this->PostpondDelivery) {
            $this->OrderItemStatus = 3;
        } else {
            $this->OrderItemStatus = 1;
        }
    }
}
