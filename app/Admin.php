<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'last_name'];

    // override default values
    protected $primaryKey = 'user_id';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
