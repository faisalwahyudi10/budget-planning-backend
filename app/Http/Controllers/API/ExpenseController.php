<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\RealizationExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;
use Exception;

class ExpenseController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 10);
        $name = $request->input('name');
        $itemType = $request->input('item_type');
        $unitType = $request->input('unit_type');
        $realized = $request->input('realized');
        $activity = $request->input('activity_id');
        $withActivity = $request->input('with_activity', false);
        $withEmployee = $request->input('with_employee', false);
        $withSum = $request->input('with_sum', false);
        $withSumReal = $request->input('with_sum_real', false);
        $orderByTw = $request->input('order_tw', false);
        $null = $request->input('is_null', false);
        $notNull = $request->input('not_null', false);
        $groupByDetail = $request->input('group_detail', false);

        // Get expense
        $expenseQuery = Expense::with('activity', 'detailType');

        if ($id) {
            $expense = $expenseQuery->with('activity.program.user.employee')->find($id);

            if ($expense) {
                return ResponseFormatter::success($expense, 'Expense found');
            }

            return ResponseFormatter::error('Expense not found', 404);
        }

        $expenses = $expenseQuery;

        if ($name) {
            $expenses->where('name', $name);
        }

        if ($itemType) {
            $expenses->where('item_type', $itemType);
        }

        if ($unitType) {
            $expenses->where('unit_type', $unitType);
        }

        if ($realized) {
            $expenses->where('realized', $realized);
        }

        if ($activity) {
            $expenses->where('activity_id', $activity);
        }

        if ($orderByTw) {
            $expenses->orderBy('tw', 'ASC');
        }

        if ($groupByDetail) {
            $expenses->select(Expense::raw('sum(cost*amount) as jumlah'), 'detail_type')->orderBy('detail_type')->groupBy('detail_type')->without('activity');
        }

        if ($withSum) {
            $expenses->select(Expense::raw('sum(cost*amount) as jumlah, tw, activity_id'))->groupBy('tw', 'activity_id')->without('activity');
        }

        if ($withSumReal) {
            $expenses->select(Expense::raw('sum(realized*amount_real) as jumlah, tw, activity_id'))->groupBy('tw', 'activity_id')->without('activity');
        }

        if ($null) {
            $expenses->where('amount_real', null)->orWhere('realized', null);
        }

        if ($notNull) {
            $expenses->whereNotNull('amount_real')->orWhereNotNull('realized');
        }

        if ($withActivity) {
            $expenses->with('activity');
        }

        if ($withEmployee) {
            $expenses->with('activity.program.user.employee');
        }

        // Return response
        return ResponseFormatter::success($expenses->paginate($limit), 'Fetch success');
    }

    public function create(CreateExpenseRequest $request)
    {
        try {
            // create expense
            $expense = Expense::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'expense_type' => $request->expense_type,
                'item_type' => $request->item_type,
                'detailType_id' => $request->detailType_id,
                'unit_type' => $request->unit_type,
                'cost' => $request->cost,
                'tw' => $request->tw,
                'activity_id' => $request->activity_id,
                'program_id' => $request->program_id,
            ]);

            if (!$expense) {
                throw new Exception('Expense not created');
            }
    
            return ResponseFormatter::success($expense, 'Expense Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateExpenseRequest $request, $id)
    {
        try {
            // Get expense
            $expense = Expense::find($id);

            // Check if expense exists
            if (!$expense) {
                throw new Exception('Expense not found');
            }
    
            // update expense
            $expense->update([
                'name' => $request->name,
                'amount' => $request->amount,
                'expense_type' => $request->expense_type,
                'item_type' => $request->item_type,
                'detailType_id' => $request->detailType_id,
                'unit_type' => $request->unit_type,
                'cost' => $request->cost,
                'tw' => $request->tw,
                'activity_id' => $request->activity_id,
            ]);

            if (!$expense) {
                throw new Exception('Expense not Updated');
            }
    
            return ResponseFormatter::success($expense, 'Expense Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function realization(RealizationExpenseRequest $request, $id)
    {
        try {
            // Get expense
            $expense = Expense::find($id);

            // Check if expense exists
            if (!$expense) {
                throw new Exception('Expense not found');
            }
    
            // update expense
            $expense->update([
                'amount_real' => $request->amount_real,
                'realized' => $request->realized,
            ]);

            if (!$expense) {
                throw new Exception('Expense not Updated');
            }
    
            return ResponseFormatter::success($expense, 'Expense Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Get expense
            $expense = Expense::find($id);

            // Check if expense exists
            if (!$expense) {
                throw new Exception('Expense not found');
            }

            // Delete expense
            $expense->delete();

            return ResponseFormatter::success('Expense Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
