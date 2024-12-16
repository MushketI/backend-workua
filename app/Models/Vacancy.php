<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'employment_type',
        'description',
        'locations_id',
        'category_id',
        'salary',
        'isActive',
    ];

    protected $casts = [
        'salary' => 'array',
        'isActive' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(City::class, 'locations_id');
    }

    public function reviews()
    {
        return $this->hasMany(VacancyReview::class, 'vacancy_id');
    }




}
