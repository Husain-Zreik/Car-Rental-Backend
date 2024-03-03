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

class CarController extends Controller
{
    public function addCar(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'plate' => 'required|string',
                'model' => 'required|integer',
                'image_base64' => 'required|string',
            ]);

            $base64Image = $request->input('image_base64');
            $decodedImage = base64_decode($base64Image);

            $filename = 'car_' . time() . '.jpg';
            $imagePath = 'cars_images/' . $filename;
            Storage::disk('public')->put($imagePath, $decodedImage);

            $car = Car::create([
                'name' => $request->name,
                'plate' => $request->plate,
                'model' => $request->model,
                'image_path' => $imagePath,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Car added successfully',
                'car' => $car,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function getCars()
    {
        $cars = Car::where('user_id', Auth::id())->get();

        $carsData = [];
        foreach ($cars as $car) {

            $rental = Rental::where('car_id', $car->id)->orderBy('start_date', 'desc')->first();

            if ($rental) {
                $client = Client::find($rental->client_id);
            }

            $imagePath = asset('storage/' . $car->image_path);

            $carsData[] = [
                'id' => $car->id,
                'name' => $car->name,
                'plate' => $car->plate,
                'model' => $car->model,
                'image_url' => $imagePath,
                'available_status' => $car->available_status ? True : False,
                'renting_info' => $car->available_status ? 'not rented' : [
                    'client_name' => $client->name,
                    'start_date' => $rental->start_date,
                    'end_date' => $rental->end_date,
                ],
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'cars retrieved successfully',
            'cars' => $carsData,
        ]);
    }

    public function getCarDetails($id)
    {
        try {
            $car = Car::findOrFail($id);
            $rentals = Rental::where('car_id', $car->id)->get();

            $rentalsData = [];
            foreach ($rentals as $rental) {
                $client = Client::findOrFail($rental->client_id);

                $sponsor = null;
                if ($client->sponsor_id) {
                    $sponsor = Sponsor::find($client->sponsor_id);
                }

                $videoPath = asset('storage/' . $rental->insurance_video_path);

                $rentalsData[] = [
                    'id' => $rental->id,
                    'name' => $client->name,
                    'number' => $client->number,
                    'sponsor' => $sponsor ? [
                        'name' => $sponsor->name,
                        'number' => $sponsor->number,
                    ] : null,
                    'start_date' => $rental->start_date,
                    'end_date' => $rental->end_date,
                    'insurance_video_url' => $videoPath,
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'cars retrieved successfully',
                'rentals' => $rentalsData,
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
