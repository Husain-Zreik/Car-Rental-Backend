<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SponsorController extends Controller
{
    public function addSponsor(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'number' => 'required|string|max:25',
            ]);

            $sponsor = Sponsor::create([
                'name' => $request->name,
                'number' => $request->number,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Sponsor created successfully',
                'sponsor' => $sponsor,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        };
    }
}
