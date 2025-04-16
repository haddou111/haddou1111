<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
    ];

    /**
     * Récupérer les réclamations faites par ce professeur
     */
    public function reclamations()
    {
        return Reclamation::query()
            ->where('type_utilisateur', 'professeur')
            ->where('utilisateur_id', $this->id)
            ->get();
    }

    /**
     * Obtenir le nom complet du professeur
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
} 