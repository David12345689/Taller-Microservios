<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    public function tokens()
    {
        return $this->hasMany(Token::class, 'user_id');
    }
}