<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'auth_tokens';  
    protected $fillable = ['user_id', 'token'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}