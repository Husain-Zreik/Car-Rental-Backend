<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Client;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RentalController extends Controller
{
    public function addRent(Request $request)
    {
        try {
            $request->validate([
                'car_id' => 'required|exists:cars,id',
                'client_id' => 'required|exists:clients,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'insurance_video' => 'nullable|file',
            ]);

            $car = Car::findOrFail($request->car_id);
            $client = Client::findOrFail($request->client_id);

            if (!$car->available_status) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The car is not available for rent',
                ], 422);
            }

            if ($client->renting_status) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The client is already renting a car',
                ], 422);
            }

            $insuranceVideoPath = null;
            if ($request->has('insurance_video')) {
                $decodedInsuranceVideo = $request->file('insurance_video');
                $insuranceVideoPath = 'insurance_videos/' . 'insurance_' . time() . '.' . $decodedInsuranceVideo->getClientOriginalExtension();
                $decodedInsuranceVideo->storeAs('public', $insuranceVideoPath);
            }

            $rental = Rental::create([
                'client_id' => $client->id,
                'car_id' => $car->id,
                'user_id' => Auth::id(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'insurance_video_path' => $insuranceVideoPath,
            ]);

            $client->renting_status = true;
            $client->save();

            $car->available_status = false;
            $car->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Rental added successfully',
                'rental' => $rental,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client or car not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
