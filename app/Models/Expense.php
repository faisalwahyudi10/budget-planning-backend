<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'amount',
        'amount_real',
        'item_type',
        'unit_type',
        'cost',
        'realized',
        'tw',
        'activity_id',
        'program_id',
        'expense_type',
        'detailType_id',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function detailType()
    {
        return $this->belongsTo(DetailType::class, 'detailType_id', 'id');
    }
}
