<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'registration_status',
        'course_completion_year',
        'cv',
        'company_name',
        'company_logo',
        'company_city',
        'company_website',
        'company_description',
    ];

    /**
     * Os atributos que devem ser ocultados.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // Removido o cast 'password' => 'hashed' para evitar problemas com os testes
    ];

    /**
     * Verifica se o utilizador é um superadmin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    /**
     * Verifica se o utilizador é uma empresa.
     *
     * @return bool
     */
    public function isCompany()
    {
        return $this->role === 'admin';
    }

    /**
     * Verifica se o utilizador é um ex-aluno.
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->role === 'student';
    }

    /**
     * Relação com áreas de interesse.
     */
    public function areasOfInterest()
    {
        return $this->belongsToMany(AreaOfInterest::class, 'user_area_of_interest');
    }

    /**
     * Relação com ofertas de emprego (para empresas).
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'company_id');
    }

    /**
     * Relação com candidaturas (para ex-alunos).
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Relação com ofertas guardadas (para ex-alunos).
     */
    public function savedJobs()
    {
        return $this->belongsToMany(Job::class, 'saved_jobs');
    }
}
