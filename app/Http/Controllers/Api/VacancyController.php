<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VacancyCollection;
use App\Http\Resources\VacancyResource;
use App\Models\Company;
use App\Models\Employer;
use App\Models\Vacancy;
use App\Models\VacancyRating;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    public function create(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'employment_type' => 'required|in:1,2',
            'description' => 'nullable|string',
            'locations_id' => 'required|exists:locations,id',
            'category_id' => 'required|exists:categories,id',
            'salary' => 'required|array',
            'salary.0' => 'required|integer|min:0',
            'salary.1' => 'required|integer|gte:salary.0',
            'isActive' => 'boolean',
        ]);

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не аутентифицирован'], 401);
        }

        $employee = $user->employers()->first();

        if (!$employee) {
            return response()->json([
                'message' => 'У пользователя нет компании для создания вакансии.'
            ], 403);
        }

        $company = $employee->company;

        $data['user_id'] = $user->id;
        $data['company_id'] = $company->id;
        $vacancy = Vacancy::create($data);

        return response()->json([
            'vacancy' => $vacancy,
            'status' => [
                'code' => 200,
                'message' => 'Вакансия успешно создана'
            ]
        ], 200);
    }

    public function getVacancies()
    {

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не аутентифицирован'], 401);
        }

        $employer = $user->employers()->first();

        if (!$employer) {
            return response()->json([
                'message' => 'Пользователь не является работодателем, и у него нет компании.'
            ], 403);
        }

        $company = $employer->company;

        $vacancies = Vacancy::where('company_id', $company->id)
            ->with('location')
            ->get();

        //Переделать на коллекции
        $vacancies = $vacancies->map(function ($vacancy) {
            // Заменяем location_id на location_name
            $vacancy->location_name = $vacancy->location ? $vacancy->location->name : null;
            unset($vacancy->location); // Убираем оригинальный объект location, если он больше не нужен

            // Заменяем category_id на category_name
            $vacancy->category_name = $vacancy->category ? $vacancy->category->name : null;
            unset($vacancy->category); // Убираем оригинальный объект category, если он больше не нужен

            $vacancy->date = $vacancy->updated_at ? $vacancy->updated_at->format('m.d.Y') : null;

            // Получаем средний рейтинг для каждой вакансии
            $vacancy->average_rating = VacancyRating::where('vacancy_id', $vacancy->id)->avg('rating');

            return $vacancy;
        });

        return response()->json([
            'vacancies' => $vacancies,
            'status' => [
                'code' => 200,
                'message' => 'Вакансии успешно получены.'
            ]
        ]);
    }

    public function toggleStatus($vacancyId)
    {

        $user = auth('sanctum')->user();

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Доступ запрещен'], 403);
        }

        $vacancy = Vacancy::where('id', $vacancyId)
            ->where('user_id', $user->id)
            ->first();

        if (!$vacancy) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Вакансия не найдена или не принадлежит вам'
                ],
            ], 404);
        }

        $vacancy->isActive = !$vacancy->isActive;
        $vacancy->save();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Статус вакансии успешно обновлен'
            ]
        ]);
    }

    public function deleteVacancy($vacancyId)
    {

        $user = auth('sanctum')->user();

        if ($user->role_id !== 2) {
            return response()->json([
                'status' => [
                    'code' => 403,
                    'message' => 'Доступ запрещен'
                ],
            ], 403);
        }

        $vacancy = Vacancy::where('id', $vacancyId)
            ->where('user_id', $user->id)
            ->first();

        if (!$vacancy) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Вакансия не найдена или не принадлежит вам'
                ]
            ], 404);
        }

        $vacancy->delete();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Вакансия успешно удалена'
            ],
        ], 200);
    }

    public function getVacancyById($id)
    {

        $user = auth('sanctum')->user();

        if ($user->role_id !== 2) {
            return response()->json([
                'message' => 'У вас нет прав'
            ], 403);
        }

        $vacancy = Vacancy::find($id);

        if (!$vacancy) {
            return response()->json([
                'message' => 'Такой вакансии не существует'
            ], 404);
        }

        $company = Employer::where('company_id', $vacancy->company_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$company) {
            return response()->json([
                'message' => 'Эта вакансия не принадлежит текущей компании'
            ], 403);
        }


        return response()->json([
            'vacancy' => $vacancy
        ], 200);
    }

    public function updateVacancy(Request $request, $id)
    {

        $vacancy = Vacancy::find($id);

        if (!$vacancy) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Вакансия не найдена'
                ],
            ], 404);
        }

        // Получение текущего пользователя
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => [
                    'code' => 401,
                    'message' => 'Пользователь не аутентифицирован'
                ]
            ], 401);
        }

        $employer = $user->employers()->first();
        if (!$employer || $employer->company_id !== $vacancy->company_id) {
            return response()->json([
                'status' => [
                    'code' => 403,
                    'message' => 'Вы не авторизованы для изменения этой вакансии'
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'employment_type' => 'required|integer|in:1,2',
            'category_id' => 'required|integer|exists:categories,id',
            'locations_id' => 'required|integer|exists:locations,id',
            'salary' => 'required|array|size:2',
            'salary.0' => 'required|integer|min:0',
            'salary.1' => 'required|integer|gte:salary.0',
            'description' => 'required|string|min:10|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vacancy->update([
            'name' => $request->input('name'),
            'employment_type' => $request->input('employment_type'),
            'category_id' => $request->input('category_id'),
            'locations_id' => $request->input('locations_id'),
            'salary' => $request->input('salary'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Вакансия успешно обновлена'
            ],
        ], 200);
    }

    public function getAllVacancies(Request $request)
    {
        $query = Vacancy::where('isActive', 1);

        $user = auth('sanctum')->user();

        $candidateCategoryId = null;

        //Алгоритм
        if ($user && $user->candidate) {
            $candidateCategoryId = $user->candidate->category_id ?? null;
        }

        if ($candidateCategoryId && !$request->has('category')) {
            $query->where('category_id', $candidateCategoryId);
        }

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        if ($request->has('category') && $request->input('category') !== 'all') {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->has('city')) {
            $query->where('locations_id', $request->input('city'));
        }

        if ($request->has('employment')) {
            $query->where('employment_type', $request->input('employment'));
        }

        if ($request->has('sort')) {
            $sortOrder = $request->input('sort') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('created_at', $sortOrder);
        }

        $perPage = $request->input('per_page', 5);

        $vacancies = $query->paginate($perPage);

        return new VacancyCollection($vacancies);
    }

    public function getSingVacancy($id)
    {
        $vacancy = Vacancy::find($id);

        if (!$vacancy) {
            return response()->json([
                'message' => 'Вакансия не найдена'
            ], 404);
        }

        return new VacancyResource($vacancy);
    }
}
