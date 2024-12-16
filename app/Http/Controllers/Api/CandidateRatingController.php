<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidateRating;
use Illuminate\Http\Request;

class CandidateRatingController extends Controller
{
    public function createRatingForCandidate(Request $request)
    {

        $user = auth('sanctum')->user();

        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $existingRating = CandidateRating::where('candidate_id', $validated['candidate_id'])
            ->where('user_id', $user->id)
            ->first();

        if ($existingRating) {
            return response()->json(['message' => 'Вы уже оценили этого кандидата'], 400);
        }

        $validated['user_id'] = $user->id;

        $rating = CandidateRating::create($validated);

        return response()->json($rating, 201);
    }
}
