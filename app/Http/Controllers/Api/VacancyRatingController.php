<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VacancyRating;
use Illuminate\Http\Request;

class VacancyRatingController extends Controller
{
    public function createRatingForVacancy(Request $request)
    {
        $user = auth('sanctum')->user();

        $validated = $request->validate([
            'vacancy_id' => 'required|exists:vacancies,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Проверка, добавил ли пользователь уже рейтинг для этого кандидата
        $existingRating = VacancyRating::where('vacancy_id', $validated['vacancy_id'])
            ->where('user_id', $user->id)
            ->first();

        // Если рейтинг уже существует
        if ($existingRating) {
            return response()->json(['message' => 'Вы уже оценили этого кандидата'], 400);
        }

        $validated['user_id'] = $user->id;

        $rating = VacancyRating::create($validated);

        return response()->json($rating, 201);
    }
}
