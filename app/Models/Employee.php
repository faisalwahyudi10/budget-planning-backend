<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nik',
        'position',
        'gender',
        'birth_date',
        'phone',
        'photo',
        'email',
        'address',
        'is_verified',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
