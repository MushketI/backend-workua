<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => [
                    'code' => 422,
                    'message' => 'Ошибка данных, проверьте пожалуйста данные',
                ],
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth('sanctum')->user();

        if (!$user || $user->role_id !== 2) {
            return response()->json([
                'status' => [
                    'code' => 403,
                    'message' => 'Доступ запрещен. Вы не являетесь работодателем.',
                ],
            ], 403);
        }

        $employer = Employer::where('user_id', $user->id)->first();

        if (!$employer) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Работодатель не найден.',
                ],
            ], 404);
        }

        if ($employer->company_id !== null) {
            return response()->json([
                'status' => [
                    'code' => 409,
                    'message' => 'У вас уже есть зарегистрированная компания.',
                ],
            ], 409);
        }

        $company = Company::create([
            'name' => $request->name,
            'description' => $request->description,
            'isActive' => true,
            'user_id' => $user->id,
        ]);

        $employer->company_id = $company->id;
        $employer->save();

        return response()->json([
            'status' => [
                'code' => 201,
                'message' => 'Компания успешно создана и работодатель обновлен.',
            ],
            'data' => [
                'company' => $company,
                'employer' => $employer,
            ],
        ], 201);
    }

    public function getUserCompanies()
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

        $employer = Employer::where('user_id', $user->id)->first();

        if (!$employer) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Работодатель не найден.',
                ],
            ], 404);
        }

        $company = Company::find($employer->company_id);

        if (!$company) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Компания не найдена.',
                ],
            ], 404);
        }

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Компания найдена.',
            ],
            'data' => $company,
        ], 200);
    }

    public function deleteCompany($companyId)
    {
        $company = Company::findOrFail($companyId);

        $user = auth('sanctum')->user();

        $employer = Employer::where('company_id', $companyId)
            ->where('user_id', $user->id)
            ->first();

        if (!$employer) {
            return response()->json(['error' => 'Вы не можете удалить эту компанию.'], 403);
        }

        if ($company->vacancies()->exists()) {
            $company->vacancies()->delete();
        }

        Employer::where('company_id', $companyId)->update(['company_id' => null]);

        $company->delete();

        return response()->json(['message' => 'Компания, её вакансии и связи успешно удалены.'], 200);
    }


}
