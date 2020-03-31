<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Credential extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['login', 'password', 'token'];

    // override default values
    protected $primaryKey = 'user_id';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
