<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Etudiant extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nom',
        'prenom',
        'email',
        'cne',
        'filiere_id',
        'semestre',
        'annee',
    ];

    protected $casts = [
        'annee' => 'integer',
    ];

    /**
     * Indique que l'ID est déjà défini et ne devrait pas être auto-incrémenté
     */
    public $incrementing = false;

    /**
     * Récupérer la filière de cet étudiant
     */
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    /**
     * Récupérer les réclamations faites par cet étudiant
     */
    public function reclamations()
    {
        return Reclamation::query()
            ->where('type_utilisateur', 'etudiant')
            ->where('utilisateur_id', $this->id)
            ->get();
    }

    /**
     * Obtenir le nom complet de l'étudiant
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
} 