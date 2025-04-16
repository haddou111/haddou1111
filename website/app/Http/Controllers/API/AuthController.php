<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Administration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Connecter un administrateur et générer un token Sanctum
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
                'device_name' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $admin = Administration::where('email', $request->email)->first();

            if (!$admin || !Hash::check($request->password, $admin->mot_de_passe)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les identifiants fournis sont incorrects'
                ], 401);
            }

            $deviceName = $request->device_name ?? $request->ip();

            // Supprimer les anciens tokens associés à cet appareil (optionnel)
            $admin->tokens()->where('name', $deviceName)->delete();

            // Générer un nouveau token
            $token = $admin->createToken($deviceName)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => [
                    'admin' => [
                        'id' => $admin->id,
                        'nom' => $admin->nom,
                        'email' => $admin->email,
                    ],
                    'token' => $token
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de serveur',
                'error' => $e->getMessage() // En développement, pour déboguer
            ], 500);
        }
    }

    /**
     * Déconnecter l'administrateur (révoquer le token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Supprimer le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Récupérer les informations de l'administrateur connecté
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $admin = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $admin->id,
                'nom' => $admin->nom,
                'email' => $admin->email,
            ]
        ]);
    }
} 