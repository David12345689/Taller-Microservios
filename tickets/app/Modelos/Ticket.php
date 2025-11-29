<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'gestor_id',
        'admin_id'
    ];
    public $timestamps = true;

    // Relación con actividades
    public function actividades()
    {
        return $this->hasMany(ActividadTicket::class, 'ticket_id');
    }
    public function actividad()
    {
        return $this->hasMany(\App\Modelos\ActividadTicket::class, 'ticket_id');
    }
}
