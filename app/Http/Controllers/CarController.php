<?php

namespace App\Http\Controllers;

use App\Models\Car;
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
                'model' => 'required|number',
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
        $user = Auth::user();

        $cars = Car::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'cars retrieved successfully',
            'cars' => $cars,
        ]);
    }
}
