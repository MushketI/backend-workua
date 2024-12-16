<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateCollection;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CandidateController extends Controller
{
    public function createCandidateInfo(Request $request)
    {

        $user = auth('sanctum')->user();

        if ($user->role_id !== 1) {
            return response()->json(['error' => 'Недостаточно прав'], 403);
        }

        $candidate = Candidate::where('user_id', $user->id)->first();


        if (!$candidate) {
            return response()->json(['error' => 'Кандидат не найден'], 404);
        }

        if ($candidate->resume) {
            return response()->json([
                'status' => [
                    'code' => 400,
                    'message' => 'Резюме уже существует'
                ],
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'resume' => 'required|string|max:1000',
            'category_id' => 'required|integer|exists:categories,id',
            'skills' => 'required|array|min:1',
            'skills.*' => 'string|max:255',
            'education' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $candidate->resume = $request->input('resume');
        $candidate->category_id = $request->input('category_id');
        $candidate->skills = implode(',', $request->input('skills'));
        $candidate->education = $request->input('education');
        $candidate->save();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Резюму создано'
            ]
        ], 200);
    }

    public function getCandidate(Request $request)
    {

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => [
                    'code' => 401,
                    'message' => 'Пользователь не авторизован.',
                ],
            ], 401);
        }

        if ($user->role_id !== 1) {
            return response()->json(['error' => 'Недостаточно прав'], 403);
        }

        $candidate = Candidate::with('category')->where('user_id', $user->id)->first();

        if (!$candidate) {
            return response()->json(['error' => 'Кандидат не найден'], 404);
        }

        $candidate->category_name = $candidate->category ? $candidate->category->name : null;

        return response()->json($candidate, 200);
    }

    public function updateCandidateInfo(Request $request)
    {

        $user = auth('sanctum')->user();

        if ($user->role_id !== 1) {
            return response()->json(['error' => 'Недостаточно прав'], 403);
        }

        $candidate = Candidate::where('user_id', $user->id)->first();

        if (!$candidate) {
            return response()->json(['error' => 'Кандидат не найден'], 404);
        }

        $validator = Validator::make($request->all(), [
            'resume' => 'nullable|string|max:1000', // Поля могут быть необязательными
            'category_id' => 'nullable|integer|exists:categories,id',
            'skills' => 'nullable|array|min:1',
            'skills.*' => 'string|max:255',
            'education' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if ($request->has('resume')) {
            $candidate->resume = $request->input('resume');
        }

        if ($request->has('category_id')) {
            $candidate->category_id = $request->input('category_id');
        }

        if ($request->has('skills')) {
            $candidate->skills = implode(',', $request->input('skills'));
        }

        if ($request->has('education')) {
            $candidate->education = $request->input('education');
        }

        $candidate->save();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Информация успешно обновлена'
            ]
        ], 200);
    }

    public function toggleCandidateStatus()
    {

        $user = auth('sanctum')->user();

        if ($user->role_id !== 1) {
            return response()->json(['message' => 'Доступ запрещен'], 403);
        }

        $candidate = Candidate::where('user_id', $user->id)->first();

        if (!$candidate) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Кандидат не найден'
                ],
            ], 404);
        }

        $candidate->isActive = !$candidate->isActive;
        $candidate->save();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Статус кандидата успешно обновлен'
            ]
        ]);
    }

    public function clearCandidateInfo()
    {

        $user = auth('sanctum')->user();


        if ($user->role_id !== 1) {
            return response()->json(['message' => 'Доступ запрещен'], 403);
        }

        $candidate = Candidate::where('user_id', $user->id)->first();

        if (!$candidate) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Кандидат не найден'
                ],
            ], 404);
        }


        $candidate->resume = null;
        $candidate->category_id = null;
        $candidate->skills = null;
        $candidate->education = false;
        $candidate->isActive = false;
        $candidate->save();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Данные кандидата успешно обновлены'
            ]
        ]);
    }

    public function getAllCandidates(Request $request)
    {
        $query = Candidate::where('isActive', 1);

        $user = auth('sanctum')->user();

        //Алгоритм кандидатов
        if ($user && $user->role_id == 2) {
            $vacancyCategoryIds = Vacancy::where('user_id', $user->id)
                ->where('isActive', 1)
                ->pluck('category_id')
                ->unique();

            if ($vacancyCategoryIds) {
                $query->whereIn('category_id', $vacancyCategoryIds);
            } else {
                return response()->json(['data' => [], 'message' => 'Нет подходящих кандидатов'], 200);
            }
        }


        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('resume', 'LIKE', '%' . $searchTerm . '%');
        }

        if ($request->has('category') && $request->input('category') !== 'all') {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->has('education')) {
            $education = (int) $request->input('education'); // Преобразуем строку в число
            $query->where('education', $education);
        }

        if ($request->has('sort')) {
            $sortOrder = $request->input('sort') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('created_at', $sortOrder);
        }

        $perPage = $request->input('per_page', 5);

        $candidates = $query->paginate($perPage);

        return new CandidateCollection($candidates);
    }

    public function getSingCandidate($id)
    {

        $candidate = Candidate::find($id);

        if (!$candidate) {
            return response()->json([
                'message' => 'Кандидат не найдена'
            ], 404);
        }

        return new CandidateResource($candidate);
    }

}
