<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_utilisateur',
        'utilisateur_id',
        'sujet',
        'message',
        'statut',
        'date_creation',
    ];

    protected $casts = [
        'date_creation' => 'datetime',
    ];

    /**
     * Récupérer l'utilisateur (étudiant ou professeur) qui a fait cette réclamation
     */
    public function utilisateur()
    {
        if ($this->type_utilisateur === 'etudiant') {
            return Etudiant::find($this->utilisateur_id);
        } elseif ($this->type_utilisateur === 'professeur') {
            return Professeur::find($this->utilisateur_id);
        }
        
        return null;
    }

    /**
     * Scope pour filtrer les réclamations par statut
     */
    public function scopeParStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour filtrer les réclamations par type d'utilisateur
     */
    public function scopeParTypeUtilisateur($query, string $type)
    {
        return $query->where('type_utilisateur', $type);
    }
} 