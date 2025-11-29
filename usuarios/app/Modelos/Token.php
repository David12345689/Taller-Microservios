<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'auth_tokens';
    protected $fillable = ['user_id', 'token'];
    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}