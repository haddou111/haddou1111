<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvisResource;
use App\Models\Avis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvisController extends Controller
{
    /**
     * Récupérer la liste de tous les avis
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $avis = Avis::with('admin')->get();
        
        return AvisResource::collection($avis);
    }

    /**
     * Récupérer les avis récents pour la page d'accueil
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function recent(Request $request): AnonymousResourceCollection
    {
        // Par défaut, récupère les 5 derniers avis, mais permet de le personnaliser via le paramètre limit
        $limit = $request->input('limit', 5);
        
        $avis = Avis::with('admin')
            ->orderBy('date_publication', 'desc')
            ->take($limit)
            ->get();
        
        return AvisResource::collection($avis);
    }

    /**
     * Créer un nouvel avis
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Vérifier si image_url contient un fichier par erreur
            if ($request->hasFile('image_url')) {
                // Transférer le fichier au champ image
                $request->merge(['image' => $request->file('image_url')]);
                // Supprimer image_url
                $request->request->remove('image_url');
            }

            $validator = Validator::make($request->all(), [
                'titre' => 'required|string|max:150',
                'contenu' => 'required|string',
                'image_url' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'lien_associe' => 'nullable|string|max:255',
                'date_publication' => 'nullable|date',
                'admin_id' => 'required|exists:administration,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();

            // Traitement de l'image si elle est fournie
            if ($request->hasFile('image')) {
                // Générer un nom de fichier unique
                $fileName = 'avis_' . Str::slug($request->titre) . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();
                
                // Stocker l'image
                $path = $request->file('image')->storeAs('public/avis', $fileName);
                
                // Remplacer image_url par l'URL du fichier téléchargé
                $data['image_url'] = Storage::url($path);
            }

            // Supprimer le champ image du tableau de données car il n'existe pas dans la base de données
            if (isset($data['image'])) {
                unset($data['image']);
            }

            $avis = Avis::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Avis créé avec succès',
                'data' => new AvisResource($avis)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'avis',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Récupérer un avis spécifique
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $avis = Avis::with('admin')->find($id);
        
        if (!$avis) {
            return response()->json([
                'success' => false,
                'message' => 'Avis non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new AvisResource($avis)
        ]);
    }

    /**
     * Mettre à jour un avis existant
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Vérifier si image_url contient un fichier par erreur
            if ($request->hasFile('image_url')) {
                // Transférer le fichier au champ image
                $request->merge(['image' => $request->file('image_url')]);
                // Supprimer image_url
                $request->request->remove('image_url');
            }
            
            $avis = Avis::find($id);
            
            if (!$avis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Avis non trouvé'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'titre' => 'sometimes|required|string|max:150',
                'contenu' => 'sometimes|required|string',
                'image_url' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'lien_associe' => 'nullable|string|max:255',
                'date_publication' => 'nullable|date',
                'admin_id' => 'sometimes|required|exists:administration,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();

            // Traitement de l'image si elle est fournie
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                if ($avis->image_url) {
                    $oldPath = str_replace('/storage', 'public', $avis->image_url);
                    if (Storage::exists($oldPath)) {
                        Storage::delete($oldPath);
                    }
                }

                // Générer un nom de fichier unique
                $fileName = 'avis_' . Str::slug($request->titre ?? $avis->titre) . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();
                
                // Stocker la nouvelle image
                $path = $request->file('image')->storeAs('public/avis', $fileName);
                
                // Remplacer image_url par l'URL du fichier téléchargé
                $data['image_url'] = Storage::url($path);
            }

            // Supprimer le champ image du tableau de données car il n'existe pas dans la base de données
            if (isset($data['image'])) {
                unset($data['image']);
            }

            $avis->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Avis mis à jour avec succès',
                'data' => new AvisResource($avis)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'avis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un avis
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $avis = Avis::find($id);
            
            if (!$avis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Avis non trouvé'
                ], 404);
            }

            // Supprimer l'image associée si elle existe
            if ($avis->image_url) {
                $path = str_replace('/storage', 'public', $avis->image_url);
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }

            $avis->delete();

            return response()->json([
                'success' => true,
                'message' => 'Avis supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'avis',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 