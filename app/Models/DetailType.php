<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detail_type';

    protected $fillable = [
        'name',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
