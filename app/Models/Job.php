<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'location',
        'salary',
        'contract_type',
        'expiration_date',
        'category_id',
        'is_active',
        'views_count',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expiration_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relação com a empresa.
     */
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    /**
     * Relação com a categoria (área de interesse).
     */
    public function category()
    {
        return $this->belongsTo(AreaOfInterest::class, 'category_id');
    }

    /**
     * Relação com candidaturas.
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Relação com ex-alunos que guardaram esta oferta.
     */
    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_jobs');
    }

    /**
     * Verifica se a oferta está ativa e não expirada.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->is_active && $this->expiration_date->greaterThanOrEqualTo(now());
    }

    /**
     * Incrementa o contador de visualizações.
     *
     * @return void
     */
    public function incrementViews()
    {
        $this->views_count += 1;
        $this->save();
    }
}
