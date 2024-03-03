<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SponsorController extends Controller
{
    public function addSponsor(Request $request)
    {
        try {
            $request->validate([
                'sponsor_name' => 'required|string|max:255',
                'sponsor_number' => 'required|string|max:25',
            ]);

            $sponsor = Sponsor::create([
                'name' => $request->sponsor_name,
                'number' => $request->sponsor_number,
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

    public function getSponsors()
    {
        $user = Auth::user();
        $sponsors = Sponsor::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Sponsors retrieved successfully',
            'sponsors' => $sponsors,
        ]);
    }
}
