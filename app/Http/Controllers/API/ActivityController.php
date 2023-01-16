<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateActivityRequest;
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
