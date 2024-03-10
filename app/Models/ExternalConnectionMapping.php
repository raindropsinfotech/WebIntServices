<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalConnectionMapping extends Model
{
    use HasFactory;
    protected $connection = 'ticketsender';

    public function externalConnection()
    {
        return $this->belongsTo(ExternalConnection::class);
    }

    public function shopCredential()
    {
        return $this->belongsTo(Credential::class, 'shop_credential_id', 'Id');
    }

    public function paymentProviderCredential()
    {
        return $this->belongsTo(Credential::class, 'payment_provider_credential_id', 'Id');
    }

    public function mailSetting()
    {
        return $this->belongsTo(MailSetting::class, 'mail_setting_id', 'Id');
    }
}
