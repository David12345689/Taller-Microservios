<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'role'];
    public $timestamps = true;

    // Relación con los tokens
    public function tokens()
    {
        return $this->hasMany(Token::class, 'user_id');
    }

    // Relación con tickets (si se usa este modelo en el microservicio de usuarios también)
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'gestor_id'); // opcional para más adelante
    }
}