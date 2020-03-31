<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'description', 'address', 'phone_number', 'zip', 'email', 'url_response', 'city', 'state', 'country', 'client_identifier', 'token'];

    // override default values
    protected $primaryKey = 'user_id';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction', 'client_id', 'user_id');
    }
}
