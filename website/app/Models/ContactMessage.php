<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'contact_messages';

    /**
     * Indique si le modèle doit être timestampé.
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
        'nom',
        'email',
        'message'
    ];

    /**
     * Scope pour trier les messages par date de création (plus récent en premier)
     */
    public function scopeTriRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
} 