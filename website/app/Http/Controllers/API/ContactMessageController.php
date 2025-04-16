<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    /**
     * Récupérer tous les messages de contact avec filtrage et tri
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ContactMessage::query();

        // Filtrage par date
        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('created_at', [
                $request->date_debut,
                $request->date_fin
            ]);
        }

        // Tri
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $messages = $query->get();
        
        return ContactMessageResource::collection($messages);
    }

    /**
     * Récupérer un message de contact spécifique
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $message = ContactMessage::find($id);
        
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ContactMessageResource($message)
        ]);
    }

    /**
     * Créer un nouveau message de contact
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:100',
                'email' => 'required|email|max:100',
                'message' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            $data['created_at'] = now();
            
            $message = ContactMessage::create($data);

            // Ici, vous pourriez ajouter un envoi d'email de notification
            // Mail::to('admin@example.com')->send(new NewContactMessageNotification($message));

            return response()->json([
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès',
                'data' => new ContactMessageResource($message)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer un message comme lu
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function markAsRead(int $id): JsonResponse
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $message->update(['lu' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Message marqué comme lu',
                'data' => new ContactMessageResource($message)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un message de contact
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Message supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 