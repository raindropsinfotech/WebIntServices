<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;
    protected $connection = 'ticketsender';
    protected $table = 'EmailTemplates';
    protected $primaryKey = 'Id';

    public $timestamps = false;


    /**
     * The attributes that are enum.
     *
     * @var array
     */
    protected $enums = [
        'EmailTemplateType″″' => [
            0 => 'NA',
            1 => 'TicketsAsAttachment',
            2 => 'TicketAsDownloadLink',
            3 => 'PaymentLink',
            40 => 'ThnakYou',
            41 => 'ReviewRequest',
            51 => 'InformationRequired',
            52 => 'Delay',
            100 => 'Newsletter'
        ],
    ];

    public function externalConnection()
    {
        return $this->belongsTo(ExternalConnection::class, 'external_connection_id');
    }
}
