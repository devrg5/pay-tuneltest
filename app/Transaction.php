<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id', 'cardholder_id', 'purchase_operation_number', 'purchase_verification', 'purchase_amount', 'currency_code', 'language', 'description_products', 'state', 'cardholder_address', 'cardholder_zip', 'cardholder_city', 'cardholder_state', 'cardholder_country', 'authorization_code', 'authorization_result', 'error_code', 'error_message', 'message_to_client', 'phone'];

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id', 'user_id');
    }

    public function cardholder()
    {
        return $this->belongsTo('App\Cardholder', 'cardholder_id', 'email');
    }
}
