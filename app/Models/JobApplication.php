<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'job_id',
        'name',
        'email',
        'phone',
        'course_completion_year',
        'message',
        'cv',
        'status',
        'processed_at',
        'notes',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Relação com o utilizador (ex-aluno).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação com a oferta de emprego.
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Verifica se a candidatura está pendente.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Verifica se a candidatura foi aceite.
     *
     * @return bool
     */
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    /**
     * Verifica se a candidatura foi rejeitada.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
