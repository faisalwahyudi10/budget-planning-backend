<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateActivityRequest;
use App\Http\Requests\RealizationActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Exception;
class ActivityController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 10);
        $name = $request->input('name');
        $program = $request->input('program_id');
        $withEmployee = $request->input('with_employee', false);
        $withExpenses1 = $request->input('with_expense1', false);
        $withExpenses2 = $request->input('with_expense2', false);
        $withExpenses3 = $request->input('with_expense3', false);
        $withSemesterOp1 = $request->input('op_semester1', false);
        $withSemesterOp2 = $request->input('op_semester2', false);
        $withSemesterMo1 = $request->input('mo_semester1', false);
        $withSemesterMo2 = $request->input('mo_semester2', false);
        $notNull = $request->input('not_null', false);

        // Get activity
        $activityQuery = Activity::with('program');

        if ($id) {

            $activity = $activityQuery->with('program.user.employee')->find($id);
            

            if ($activity) {
                return ResponseFormatter::success($activity, 'Activity found');
            }

            return ResponseFormatter::error('Activity not found', 404);
        }

        $activities = $activityQuery;

        if ($name) {
            $activities->where('name', $name);
        }

        if ($program) {
            $activities->where('program_id', $program);
        }

        if ($withEmployee) {
            $activities->with('program.user.employee');
        }

        if ($withSemesterOp1) {
                $activities->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')->whereIn('tw', [1,2])
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withSemesterOp2) {
                $activities->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')->whereIn('tw', [3,4])
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }

            if ($withSemesterMo1) {
                $activities->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')->whereIn('tw', [1,2])
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withSemesterMo2) {
                $activities->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')->whereIn('tw', [3,4])
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }

        if ($notNull) {
            
        }

        if ($withExpenses1) {
            $activities->with(['expenses'])->get();
        }

        if ($withExpenses2) {
            $activities->with(['expenses' => function ($query) {
                $query->whereNotNull('realized');
            }])->get();
        }

        if ($withExpenses3) {
            $activities->with(['expenses' => function ($query) {
                $query->whereNotNull('realized')
                ->selectRaw('sum(cost*amount) as jumlah, tw, activity_id')
                ->groupBy('tw', 'activity_id');
            }])->get();
        }

        

        // Return response
        return ResponseFormatter::success($activities->paginate($limit), 'Fetch success');
    }

    public function create(CreateActivityRequest $request)
    {
        try {
            // create activity
            $activity = Activity::create([
                'name' => $request->name,

                'activity_budget_tw1' => $request->activity_budget_tw1,
                'activity_budget_tw2' => $request->activity_budget_tw2,
                'activity_budget_tw3' => $request->activity_budget_tw3,
                'activity_budget_tw4' => $request->activity_budget_tw4,

                'activity_realized_tw1' => $request->activity_realized_tw1,
                'activity_realized_tw2' => $request->activity_realized_tw2,
                'activity_realized_tw3' => $request->activity_realized_tw3,
                'activity_realized_tw4' => $request->activity_realized_tw4,

                'document_plan_tw1' => $request->document_plan_tw1,
                'document_plan_tw2' => $request->document_plan_tw2,
                'document_plan_tw3' => $request->document_plan_tw3,
                'document_plan_tw4' => $request->document_plan_tw4,

                'document_realized_tw1' => $request->document_realized_tw1,
                'document_realized_tw2' => $request->document_realized_tw2,
                'document_realized_tw3' => $request->document_realized_tw3,
                'document_realized_tw4' => $request->document_realized_tw4,

                'target' => $request->target,
                'indicator' => $request->indicator,
                'program_id' => $request->program_id,
            ]);

            if (!$activity) {
                throw new Exception('Activity not created');
            }
    
            return ResponseFormatter::success($activity, 'Activity Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateActivityRequest $request, $id)
    {
        try {
            // Get activity
            $activity = Activity::find($id);

            // Check if activity exists
            if (!$activity) {
                throw new Exception('Activity not found');
            }
    
            // update activity
            $activity->update([
                'name' => $request->name,

                'activity_budget_tw1' => $request->activity_budget_tw1,
                'activity_budget_tw2' => $request->activity_budget_tw2,
                'activity_budget_tw3' => $request->activity_budget_tw3,
                'activity_budget_tw4' => $request->activity_budget_tw4,

                'activity_realized_tw1' => $request->activity_realized_tw1,
                'activity_realized_tw2' => $request->activity_realized_tw2,
                'activity_realized_tw3' => $request->activity_realized_tw3,
                'activity_realized_tw4' => $request->activity_realized_tw4,

                'document_plan_tw1' => $request->document_plan_tw1,
                'document_plan_tw2' => $request->document_plan_tw2,
                'document_plan_tw3' => $request->document_plan_tw3,
                'document_plan_tw4' => $request->document_plan_tw4,

                'document_realized_tw1' => $request->document_realized_tw1,
                'document_realized_tw2' => $request->document_realized_tw2,
                'document_realized_tw3' => $request->document_realized_tw3,
                'document_realized_tw4' => $request->document_realized_tw4,

                'target' => $request->target,
                'indicator' => $request->indicator,
                'program_id' => $request->program_id,
            ]);

            if (!$activity) {
                throw new Exception('Activity not Updated');
            }
    
            return ResponseFormatter::success($activity, 'Activity Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function realization(RealizationActivityRequest $request, $id)
    {
        try {
            // Get activity
            $activity = Activity::find($id);

            // Check if activity exists
            if (!$activity) {
                throw new Exception('Activity not found');
            }
    
            // update activity
            $activity->update([
                'activity_realized_tw1' => $request->activity_realized_tw1,
                'activity_realized_tw2' => $request->activity_realized_tw2,
                'activity_realized_tw3' => $request->activity_realized_tw3,
                'activity_realized_tw4' => $request->activity_realized_tw4,
                'document_realized_tw1' => $request->document_realized_tw1,
                'document_realized_tw2' => $request->document_realized_tw2,
                'document_realized_tw3' => $request->document_realized_tw3,
                'document_realized_tw4' => $request->document_realized_tw4,
            ]);

            if (!$activity) {
                throw new Exception('Activity not Updated');
            }
    
            return ResponseFormatter::success($activity, 'Activity Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Get activity
            $activity = Activity::find($id);

            // Check if activity exists
            if (!$activity) {
                throw new Exception('Activity not found');
            }

            // Delete activity
            $activity->delete();

            return ResponseFormatter::success('Activity Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
