<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidateReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CandidateReviewController extends Controller
{
    public function createCandidateReview(Request $request)
    {

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Неавторизованный доступ'], 401);
        }

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'У вас недостаточно прав для выполнения этого действия'], 403);
        }

        $validator = Validator::make($request->all(), [
            'candidate_id' => 'required|integer|exists:candidates,id',
            'message' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $review = new CandidateReview();
        $review->candidate_id = $request->candidate_id;
        $review->user_id = $user->id;
        $review->message = $request->message;
        $review->save();

        return response()->json(['status' => [
            'code' => 200,
            'message' => 'Отзыв успешно добавлен'
        ]], 200);
    }
}
