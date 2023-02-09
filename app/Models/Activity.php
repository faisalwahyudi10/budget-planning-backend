<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'activity_budget_tw1',
        'activity_budget_tw2',
        'activity_budget_tw3',
        'activity_budget_tw4',
        'activity_realized_tw1',
        'activity_realized_tw2',
        'activity_realized_tw3',
        'activity_realized_tw4',
        'document_plan_tw1',
        'document_plan_tw2',
        'document_plan_tw3',
        'document_plan_tw4',
        'document_realized_tw1',
        'document_realized_tw2',
        'document_realized_tw3',
        'document_realized_tw4',
        'target',
        'indicator',
        'program_id',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
