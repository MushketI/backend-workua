<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\City;
use App\Models\User;
use App\Models\VacancyRating;
use App\Models\VacancyReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacancyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $user = User::find($this->user_id)->name;
        $city = City::find($this->locations_id)->name;
        $category = Category::find($this->category_id)->name;
        $phone = User::find($this->user_id)->phone;

        $reviews = $this->reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'user_id' => $review->user_id,
                'user_name' => $review->user->name, // Если настроена связь user
                'message' => $review->message,
                'created_at' => $review->created_at->format('d.m.Y'),
            ];
        });

        $averageRating = VacancyRating::where('vacancy_id', $this->id)
            ->avg('rating');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'author' => $user,
            'employment' => $this->employment_type,
            'description' => $this->description,
            'city' => $city,
            'category' => $category,
            'salary_min' => $this->salary[0],
            'salary_max' => $this->salary[1],
            'phone' => $phone,
            'reviews' => $reviews,
            'rating' => round($averageRating ?: 0, 1),
        ];

    }
}
