<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'image_url' => $this->image_url,
            'lien_associe' => $this->lien_associe,
            'date_publication' => $this->date_publication ? $this->date_publication->format('Y-m-d') : null,
            'admin' => $this->when($this->admin, [
                'id' => optional($this->admin)->id,
                'nom' => optional($this->admin)->nom,
            ], null),
        ];
    }
} 