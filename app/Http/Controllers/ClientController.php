<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    public function addClient(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'number' => 'required|string',
                'address' => 'required|string',
                'front_image_base64' => 'required|string',
                'back_image_base64' => 'nullable|string',
            ]);

            $decodedFrontImage = base64_decode($request->input('front_image_base64'));
            $frontImagePath = 'id_images/' . 'front_' . time() . '.jpg';
            Storage::disk('public')->put($frontImagePath, $decodedFrontImage);

            $backImage = $request->input('back_image_base64');
            $backImagePath = null;

            if ($backImage) {
                $decodedBackImage = base64_decode($request->input('back_image_base64'));
                $backImagePath = 'id_images/' . 'back_' . time() . '.jpg';
                Storage::disk('public')->put($backImagePath, $decodedBackImage);
            }

            $client = Client::create([
                'name' => $request->name,
                'number' => $request->number,
                'address' => $request->address,
                'front_image_path' => $frontImagePath,
                'back_image_path' => $backImagePath,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Client added successfully',
                'client' => $client,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
