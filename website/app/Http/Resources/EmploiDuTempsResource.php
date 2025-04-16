<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmploiDuTempsResource extends JsonResource
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
            'filiere' => $this->when($this->filiere, [
                'id' => optional($this->filiere)->id,
                'nom' => optional($this->filiere)->nom,
                'niveau' => optional($this->filiere)->niveau,
            ], null),
            'semestre' => $this->semestre,
            'annee_univ' => $this->annee_univ,
            'fichier_pdf' => $this->fichier_pdf,
            'admin' => $this->when($this->admin, [
                'id' => optional($this->admin)->id,
                'nom' => optional($this->admin)->nom,
            ], null),
        ];
    }
} 