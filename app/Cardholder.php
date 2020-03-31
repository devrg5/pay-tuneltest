<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cardholder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'user_commerce', 'name', 'last_name', 'user_code_payme'];

    // override default values
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';

    public function transactions()
    {
        return $this->hasMany('App\Transaction', 'cardholder_id', 'email');
    }
}
