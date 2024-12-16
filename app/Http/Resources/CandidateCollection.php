<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CandidateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($candidate) {

            $userName = User::find($candidate->user_id)->name;
            $category = Category::find($candidate->category_id)->name;

            return [
                'id' => $candidate->id,
                'name' => $userName,
                'education' => $candidate->education,
                'resume' => $candidate->resume,
                'category' => $category,
                'created_at' => $candidate->created_at->format('d.m.Y'),
                'skills' => $candidate->skills,
                'category_id' => $candidate->category_id,
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
