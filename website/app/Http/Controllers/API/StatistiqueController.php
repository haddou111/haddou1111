<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Professeur;
use App\Models\Etudiant;
use Illuminate\Http\JsonResponse;

class StatistiqueController extends Controller
{
    /**
     * Récupérer les statistiques des professeurs et étudiants
     * 
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        try {
            $nombreProfesseurs = Professeur::count();
            $nombreEtudiants = Etudiant::count();

            return response()->json([
                'success' => true,
                'data' => [
                    'professeurs' => [
                        'total' => $nombreProfesseurs
                    ],
                    'etudiants' => [
                        'total' => $nombreEtudiants
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques détaillées des étudiants par filière
     * 
     * @return JsonResponse
     */
    public function getStatsEtudiants(): JsonResponse
    {
        try {
            $stats = Etudiant::select('filiere_id')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('filiere_id')
                ->with('filiere:id,nom')
                ->get()
                ->map(function($item) {
                    return [
                        'filiere' => $item->filiere->nom,
                        'total' => $item->total
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => Etudiant::count(),
                    'par_filiere' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques des étudiants',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques détaillées des professeurs
     * 
     * @return JsonResponse
     */
    public function getStatsProfesseurs(): JsonResponse
    {
        try {
            $nombreProfesseurs = Professeur::count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $nombreProfesseurs
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques des professeurs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 