<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'image'];

    public function client()
    {
        return $this->hasOne('App\Client', 'user_id');
    }

    public function admin()
    {
        return $this->hasOne('App\Admin', 'user_id');
    }

    public function credential()
    {
        return $this->hasOne('App\Credential', 'user_id');
    }

    public function addAdmin()
    {

    }

    public function addClient()
    {
        
    }
}
