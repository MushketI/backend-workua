<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'isActive', 'user_id'];

    public function employer()
    {
        return $this->hasMany(Employer::class);
    }

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class);
    }
}
