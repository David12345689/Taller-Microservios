<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class ActividadTicket extends Model
{
    protected $table = 'ticket_actividad';
    protected $fillable = [
        'ticket_id',
        'user_id',
        'mensaje'
    ];
    public $timestamps = true;

    // Relación inversa con ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}