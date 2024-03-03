<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Client;
use App\Models\Rental;
use App\Models\Sponsor;
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
                'sponsor_id' => 'nullable|integer',
                'sponsor_name' => 'nullable|string',
                'sponsor_number' => 'nullable|string',
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

            $sponsor_id = $request->sponsor_id;

            if (!$request->sponsor_id && $request->sponsor_name && $request->sponsor_number) {
                $sponsorController = new SponsorController();
                $sponsorResponse = $sponsorController->addSponsor($request);

                if ($sponsorResponse->status() === 200) {
                    $sponsorData = $sponsorResponse->getData();
                    $sponsor_id = $sponsorData->sponsor->id;
                } else {
                    throw new \Exception('Failed to create sponsor');
                }
            }

            $client = Client::create([
                'name' => $request->name,
                'number' => $request->number,
                'address' => $request->address,
                'front_image_path' => $frontImagePath,
                'back_image_path' => $backImagePath,
                'sponsor_id' => $sponsor_id,
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

    public function getClientDetails($id)
    {
        try {
            $client = Client::findOrFail($id);
            $rental = Rental::where('client_id', $id)->orderBy('start_date', 'desc')->first();

            $car = null;
            if ($rental) {
                $car = Car::where('id', $rental->car_id);
            }

            $sponsor = null;
            if ($client->sponsor_id) {
                $sponsor = Sponsor::find($client->sponsor_id);
            }

            $frontImagePath = asset('storage/' . $client->front_image_path);
            $backImagePath = null;
            if ($client->back_image_path) {
                $backImagePath = asset('storage/' . $client->back_image_path);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Client details retrieved successfully',
                'client' => [
                    'name' => $client->name,
                    'number' => $client->number,
                    'address' => $client->address,
                    'front_image_path' => $frontImagePath,
                    'back_image_path' => $backImagePath,
                    'sponsor' => $sponsor ? [
                        'name' => $sponsor->name,
                        'number' => $sponsor->number,
                    ] : null,
                    'renting_status' => $client->renting_status ? True : False,
                    'rented_car' => $client->renting_status ? [
                        'name' => $car->name,
                        'start_date' => $rental->start_date,
                        'end_date' => $rental->end_date,
                    ] : null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve client details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
