<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CarController extends Controller
{
    public function addCar(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'plate' => 'required|string',
                'model' => 'required|string',
                'image_url' => 'required|string',
            ]);

            $car = Car::create([
                'name' => $request->name,
                'plate' => $request->plate,
                'model' => $request->model,
                'image_url' => $request->image_url,
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
}
