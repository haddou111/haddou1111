<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evenement extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'evenements';

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
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'lieu',
        'image_url',
        'admin_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Récupérer l'administrateur qui a ajouté cet événement.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Administration::class, 'admin_id');
    }

    /**
     * Vérifie si l'événement est en cours
     */
    public function getEstEnCoursAttribute(): bool
    {
        $aujourdhui = now()->startOfDay();
        return $aujourdhui->between($this->date_debut, $this->date_fin);
    }

    /**
     * Vérifie si l'événement est à venir
     */
    public function getEstAVenirAttribute(): bool
    {
        $aujourdhui = now()->startOfDay();
        return $aujourdhui->lt($this->date_debut);
    }
} 