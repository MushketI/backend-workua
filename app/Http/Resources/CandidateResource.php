<?php

namespace App\Http\Resources;

use App\Models\CandidateRating;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::find($this->user_id)->name;
        $id = User::find($this->user_id)->id;
        $category = Category::find($this->category_id)->name;
        $phone = User::find($this->user_id)->phone;

        // Получаем все отзывы, связанные с вакансией
        $reviews = $this->reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'user_id' => $review->user_id,
                'user_name' => $review->user->name,
                'message' => $review->message,
                'created_at' => $review->created_at->format('d.m.Y'),
            ];
        });

        //делаем общий рейтинг
        $averageRating = CandidateRating::where('candidate_id', $this->id)
            ->avg('rating');

       return [
           'id' => $this->id,
           'name' => $user,
           'user_id' => $id,
           'resume' => $this->resume,
           'skills' => $this->skills,
           'education' => $this->education,
           'category' => $category,
           'phone' => $phone,
           'reviews' => $reviews,
           'rating' => round($averageRating ?: 0, 1),
       ];
    }
}
