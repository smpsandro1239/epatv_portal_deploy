<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationWindow extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start_time',
        'end_time',
        'max_registrations',
        'current_registrations',
        'password',
        'is_active',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Verifica se a janela de registo está aberta.
     *
     * @return bool
     */
    public function isOpen()
    {
        $now = now();
        return $this->is_active && 
               $now->greaterThanOrEqualTo($this->start_time) && 
               $now->lessThanOrEqualTo($this->end_time) &&
               ($this->max_registrations === null || $this->current_registrations < $this->max_registrations);
    }

    /**
     * Incrementa o contador de registos.
     *
     * @return void
     */
    public function incrementRegistrations()
    {
        $this->current_registrations += 1;
        $this->save();
    }
}
