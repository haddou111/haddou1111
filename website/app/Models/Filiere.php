<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'departement_id',
        'description',
        'niveau',
        'date_creation',
    ];

    protected $casts = [
        'date_creation' => 'date',
    ];

    /**
     * Récupérer le département associé à cette filière
     */
    public function departement(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departement_id');
    }

    /**
     * Récupérer les étudiants associés à cette filière
     */
    public function etudiants(): HasMany
    {
        return $this->hasMany(Etudiant::class, 'filiere_id');
    }

    /**
     * Récupérer les emplois du temps associés à cette filière
     */
    public function emploisDuTemps(): HasMany
    {
        return $this->hasMany(EmploiDuTemps::class, 'filiere_id');
    }
} 