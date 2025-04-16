<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avis extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'contenu',
        'image_url',
        'lien_associe',
        'date_publication',
        'admin_id',
    ];

    protected $casts = [
        'date_publication' => 'date',
    ];

    /**
     * Indique si le modèle doit utiliser les timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Récupérer l'administrateur qui a publié cet avis
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Administration::class, 'admin_id');
    }
} 