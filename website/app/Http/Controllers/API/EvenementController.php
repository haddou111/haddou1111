<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EvenementResource;
use App\Models\Evenement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EvenementController extends Controller
{
    /**
     * Récupérer tous les événements
     * 
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        try {
            $query = Evenement::with('admin');

            // Filtrer par date si spécifié
            if ($request->has('date')) {
                $date = $request->date;
                $query->where(function($q) use ($date) {
                    $q->whereDate('date_debut', '<=', $date)
                      ->whereDate('date_fin', '>=', $date);
                });
            }

            $evenements = $query->get();

            return EvenementResource::collection($evenements);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Récupérer un événement spécifique
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $evenement = Evenement::with('admin')->find($id);

            if (!$evenement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new EvenementResource($evenement)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'événement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouvel événement
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'lieu' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
                'admin_id' => 'required|exists:administration,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Générer un nom de fichier unique
            $fileName = 'event_' . Str::slug($request->titre) . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();
            
            // Stocker l'image
            $path = $request->file('image')->storeAs('public/evenements', $fileName);
            
            // Créer l'événement
            $evenement = Evenement::create([
                'titre' => $request->titre,
                'description' => $request->description,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'lieu' => $request->lieu,
                'image_url' => Storage::url($path),
                'admin_id' => $request->admin_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Événement créé avec succès',
                'data' => new EvenementResource($evenement)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'événement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un événement
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $evenement = Evenement::find($id);

            if (!$evenement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'titre' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'date_debut' => 'sometimes|required|date',
                'date_fin' => 'sometimes|required|date|after_or_equal:date_debut',
                'lieu' => 'sometimes|required|string|max:255',
                'image' => 'sometimes|required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
                'admin_id' => 'sometimes|required|exists:administration,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Si une nouvelle image est fournie, supprimer l'ancienne et stocker la nouvelle
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                $oldPath = str_replace('/storage', 'public', $evenement->image_url);
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }

                // Générer un nom de fichier unique
                $fileName = 'event_' . Str::slug($request->titre ?? $evenement->titre) . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();
                
                // Stocker la nouvelle image
                $path = $request->file('image')->storeAs('public/evenements', $fileName);
                $request->merge(['image_url' => Storage::url($path)]);
            }

            // Mettre à jour l'événement
            $evenement->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Événement mis à jour avec succès',
                'data' => new EvenementResource($evenement)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'événement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un événement
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $evenement = Evenement::find($id);

            if (!$evenement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé'
                ], 404);
            }

            // Supprimer l'image associée
            $path = str_replace('/storage', 'public', $evenement->image_url);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            // Supprimer l'événement
            $evenement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Événement supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'événement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 