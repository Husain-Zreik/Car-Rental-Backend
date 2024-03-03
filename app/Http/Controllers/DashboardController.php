<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Client;
use App\Models\Rental;
use App\Models\Sponsor;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getDashboardInfo()
    {
        try {
            $userId = Auth::id();
            $clients = Client::where('user_id', $userId)->get();
            $cars = Car::where('user_id', $userId)->get();
            $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
            $totalIncome = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');

            $clientsData = [];
            foreach ($clients as $client) {
                $rentals = Rental::where('client_id', $client->id)->get();
                $rental = Rental::where('client_id', $client->id)->orderBy('start_date', 'desc')->first();

                $car = null;
                if ($rental) {
                    $car = Car::find($rental->car_id);
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

                $clientsData[] = [
                    'id' => $client->id,
                    'name' => $client->name,
                    'number' => $client->number,
                    'address' => $client->address,
                    'created_at' => $client->created_at,
                    'front_image_path' => $frontImagePath,
                    'back_image_path' => $backImagePath,
                    'sponsor' => $sponsor ? [
                        'name' => $sponsor->name,
                        'number' => $sponsor->number,
                    ] : null,
                    'rentals_count' => count($rentals),
                    'renting_status' => $client->renting_status ? True : False,
                    'rented_car' => $client->renting_status ? [
                        'name' => $car->name,
                        'start_date' => $rental->start_date,
                        'end_date' => $rental->end_date,
                    ] : null,
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Clients details retrieved successfully',
                'total_clients' => count($clients),
                'total_cars' => count($cars),
                'total_expense' => $totalExpense,
                'total_income' => $totalIncome,
                'clients' => $clientsData,
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
