<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploiDuTemps extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'emplois_du_temps';

    /**
     * Désactiver les timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filiere_id',
        'semestre',
        'annee_univ',
        'fichier_pdf',
        'admin_id',
    ];

    /**
     * Récupérer la filière associée à cet emploi du temps
     */
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    /**
     * Récupérer l'administrateur qui a créé cet emploi du temps
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Administration::class, 'admin_id');
    }
} 