<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmploiDuTempsResource;
use App\Models\EmploiDuTemps;
use App\Models\Filiere;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmploiDuTempsController extends Controller
{
    /**
     * Récupérer tous les emplois du temps
     * 
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        try {
            $query = EmploiDuTemps::with('filiere', 'admin');

            // Filtrer par filière si spécifié
            if ($request->has('filiere_id')) {
                $query->where('filiere_id', $request->filiere_id);
            }

            // Filtrer par semestre si spécifié
            if ($request->has('semestre')) {
                $query->where('semestre', $request->semestre);
            }

            // Filtrer par année universitaire si spécifié
            if ($request->has('annee_univ')) {
                $query->where('annee_univ', $request->annee_univ);
            }

            $emploisDuTemps = $query->get();

            return EmploiDuTempsResource::collection($emploisDuTemps);
        } catch (\Exception $e) {
            // En cas d'erreur, on laisse l'exception remonter pour être gérée par le gestionnaire global
            throw $e;
        }
    }

    /**
     * Récupérer un emploi du temps spécifique
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $emploiDuTemps = EmploiDuTemps::with('filiere', 'admin')->find($id);

            if (!$emploiDuTemps) {
                return response()->json([
                    'success' => false,
                    'message' => 'Emploi du temps non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new EmploiDuTempsResource($emploiDuTemps)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'emploi du temps',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouvel emploi du temps
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'filiere_id' => 'required|exists:filieres,id',
                'semestre' => 'required|in:S1,S2,S3,S4,S5,S6',
                'annee_univ' => 'required|string|max:9',
                'fichier_pdf' => 'required|file|mimes:pdf|max:10240', // Max 10MB
                'admin_id' => 'required|exists:administration,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier si la filière existe
            $filiere = Filiere::find($request->filiere_id);
            if (!$filiere) {
                return response()->json([
                    'success' => false,
                    'message' => 'Filière non trouvée'
                ], 404);
            }

            // Générer un nom de fichier unique
            $fileName = 'edt_' . $filiere->nom . '_' . $request->semestre . '_' . $request->annee_univ . '_' . time() . '.' . $request->file('fichier_pdf')->getClientOriginalExtension();
            
            // Remplacer les espaces et caractères spéciaux dans le nom de fichier
            $fileName = Str::slug(pathinfo($fileName, PATHINFO_FILENAME)) . '.' . $request->file('fichier_pdf')->getClientOriginalExtension();
            
            // Stocker le fichier
            $path = $request->file('fichier_pdf')->storeAs('public/emplois_du_temps', $fileName);
            
            // Créer l'emploi du temps
            $emploiDuTemps = EmploiDuTemps::create([
                'filiere_id' => $request->filiere_id,
                'semestre' => $request->semestre,
                'annee_univ' => $request->annee_univ,
                'fichier_pdf' => Storage::url($path),
                'admin_id' => $request->admin_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Emploi du temps créé avec succès',
                'data' => new EmploiDuTempsResource($emploiDuTemps)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'emploi du temps',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un emploi du temps
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $emploiDuTemps = EmploiDuTemps::find($id);

            if (!$emploiDuTemps) {
                return response()->json([
                    'success' => false,
                    'message' => 'Emploi du temps non trouvé'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'filiere_id' => 'sometimes|required|exists:filieres,id',
                'semestre' => 'sometimes|required|in:S1,S2,S3,S4,S5,S6',
                'annee_univ' => 'sometimes|required|string|max:9',
                'fichier_pdf' => 'sometimes|required|file|mimes:pdf|max:10240', // Max 10MB
                'admin_id' => 'sometimes|required|exists:administration,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Si un nouveau fichier est fourni, supprimer l'ancien et stocker le nouveau
            if ($request->hasFile('fichier_pdf')) {
                // Supprimer l'ancien fichier s'il existe
                $oldPath = str_replace('/storage', 'public', $emploiDuTemps->fichier_pdf);
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }

                // Générer un nom de fichier unique
                $filiere = Filiere::find($request->filiere_id ?? $emploiDuTemps->filiere_id);
                $semestre = $request->semestre ?? $emploiDuTemps->semestre;
                $anneeUniv = $request->annee_univ ?? $emploiDuTemps->annee_univ;
                
                $fileName = 'edt_' . $filiere->nom . '_' . $semestre . '_' . $anneeUniv . '_' . time() . '.' . $request->file('fichier_pdf')->getClientOriginalExtension();
                $fileName = Str::slug(pathinfo($fileName, PATHINFO_FILENAME)) . '.' . $request->file('fichier_pdf')->getClientOriginalExtension();
                
                // Stocker le nouveau fichier
                $path = $request->file('fichier_pdf')->storeAs('public/emplois_du_temps', $fileName);
                $request->merge(['fichier_pdf' => Storage::url($path)]);
            } else {
                // Ne pas inclure fichier_pdf dans la mise à jour si non fourni
                $request = collect($request->except('fichier_pdf'));
            }

            // Mettre à jour l'emploi du temps
            $emploiDuTemps->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Emploi du temps mis à jour avec succès',
                'data' => new EmploiDuTempsResource($emploiDuTemps)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'emploi du temps',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un emploi du temps
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $emploiDuTemps = EmploiDuTemps::find($id);

            if (!$emploiDuTemps) {
                return response()->json([
                    'success' => false,
                    'message' => 'Emploi du temps non trouvé'
                ], 404);
            }

            // Supprimer le fichier associé
            $path = str_replace('/storage', 'public', $emploiDuTemps->fichier_pdf);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            // Supprimer l'enregistrement
            $emploiDuTemps->delete();

            return response()->json([
                'success' => true,
                'message' => 'Emploi du temps supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'emploi du temps',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les emplois du temps par filière
     * 
     * @param int $filiereId
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getByFiliere(int $filiereId)
    {
        try {
            // Vérifier si la filière existe
            $filiere = Filiere::find($filiereId);
            if (!$filiere) {
                return response()->json([
                    'success' => false,
                    'message' => 'Filière non trouvée'
                ], 404);
            }

            $emploisDuTemps = EmploiDuTemps::with('admin')
                ->where('filiere_id', $filiereId)
                ->get();

            return EmploiDuTempsResource::collection($emploisDuTemps);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des emplois du temps',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 