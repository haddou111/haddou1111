<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Télécharger une image pour un avis
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('image')) {
            // Générer un nom de fichier unique
            $fileName = 'avis_' . time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            
            // Stocker le fichier dans le dossier public/images/avis
            $path = $request->file('image')->storeAs('public/images/avis', $fileName);
            
            // Obtenir l'URL publique
            $url = Storage::url($path);

            return response()->json([
                'success' => true,
                'message' => 'Image téléchargée avec succès',
                'data' => [
                    'image_url' => $url,
                    'file_name' => $fileName
                ]
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucune image n\'a été téléchargée'
        ], 400);
    }
} 