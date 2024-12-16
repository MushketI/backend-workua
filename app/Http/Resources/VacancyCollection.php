<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VacancyCollection extends ResourceCollection
{

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {


        return $this->collection->map(function ($vacancy) {

            $companyName = Company::find($vacancy->company_id)->name;
            $userName = User::find($vacancy->user_id)->name;
            $city = City::find($vacancy->locations_id)->name;
            $category = Category::find($vacancy->category_id)->name;

            return [
                'id' => $vacancy->id,
                'name' => $vacancy->name,
                'company_name' => $companyName,
                'author' => $userName,
                'employment_type' => $vacancy->employment_type,
                'description' => $vacancy->description,
                'city' => $city,
                'category' => $category,
                'salary_min' => $vacancy->salary[0],
                'salary_max' => $vacancy->salary[1],
                'created_at' => $vacancy->created_at->format('d.m.Y'),
                'meta' => [
                    'total' => $this->resource->total(),
                    'per_page' => $this->resource->perPage(),
                    'current_page' => $this->resource->currentPage(),
                    'last_page' => $this->resource->lastPage(),
                ],
            ];
        })->toArray();
    }
}
