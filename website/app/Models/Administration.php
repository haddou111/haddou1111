<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Administration extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'administration';

    /**
     * Indique si le modèle doit utiliser les timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'email',
        'mot_de_passe',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    protected $casts = [
        'mot_de_passe' => 'hashed',
    ];

    /**
     * Récupérer les avis publiés par cet administrateur
     */
    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class, 'admin_id');
    }

    /**
     * Récupérer les événements créés par cet administrateur
     */
    public function evenements(): HasMany
    {
        return $this->hasMany(Evenement::class, 'admin_id');
    }

    /**
     * Récupérer les emplois du temps créés par cet administrateur
     */
    public function emploisDuTemps(): HasMany
    {
        return $this->hasMany(EmploiDuTemps::class, 'admin_id');
    }
} 