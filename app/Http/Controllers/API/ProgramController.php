<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProgramRequest;
use App\Http\Requests\RealizationProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Models\Program;
use Illuminate\Http\Request;
use Exception;

class ProgramController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 10);
        $name = $request->input('name');
        $date = $request->input('date_program');
        $user = $request->input('user_id');
        $withActivity = $request->input('with_activities', false);
        $withExpenseP = $request->input('with_expenses1', false);
        $withExpenseR = $request->input('with_expenses2', false);
        $withExpenseN = $request->input('with_expenses3', false);
        $withExpenseE = $request->input('with_expenses4', false);
        $realizedNull = $request->input('realized_null', false);
        $orderByYear = $request->input('order_year', false);
        $year1 = $request->input('first_year');
        $year2 = $request->input('last_year');

        // Get program
        $programQuery = Program::with('user.employee');

        if ($id) {

            if ($withExpenseP) {
                $program = $programQuery->with(['user', 'activities', 'activities.expenses' => function ($query) {
                    $query->where('realized', 1);
                }])->find($id);
            } elseif ($withExpenseR) {
                $program = $programQuery->with([
                    'user',
                    'activities' => function ($query) {
                        $query->whereNotNull('activity_realized_tw1')
                        ->orWhereNotNull('activity_realized_tw2')
                        ->orWhereNotNull('activity_realized_tw3')
                        ->orWhereNotNull('activity_realized_tw4');
                    },
                    'activities.expenses' => function ($query) {
                        $query->where('realized', 2);
                    }
                ])->find($id);
            } elseif ($withExpenseN) {
                $program = $programQuery->with([
                    'user',
                    'activities' => function ($query) {
                        $query->whereNotNull('activity_realized_tw1')
                            ->orWhereNotNull('activity_realized_tw2')
                            ->orWhereNotNull('activity_realized_tw3')
                            ->orWhereNotNull('activity_realized_tw4');
                    },
                ])->find($id);
            } elseif ($withExpenseE) {
                $program = $programQuery->with([
                    'activities' => function ($query) use ($request) {
                        $query->where('id', $request->input('activity_id'));
                    },
                    'activities.expenses' => function ($query) {
                        $query->where('realized', 2)
                        ->selectRaw('sum(cost*amount) as jumlah, tw, activity_id')
                        ->groupBy('tw', 'activity_id')
                        ->without('activity');
                    }
                ])->find($id);
            } else {
                $program = $programQuery->with(['user', 'activities'])->find($id);
            }
            

            

            if ($program) {
                return ResponseFormatter::success($program, 'Program found');
            }

            return ResponseFormatter::error('Program not found', 404);
        }

        $programs = $programQuery;

        if ($name) {
            $programs->where('name', $name);
        }
        
        if ($date) {
            $programs->where('date_program', $date);
        }

        if ($year1 && $year2) {
            $programs->whereBetween('date_program', [$year1, $year2])->orderBy('date_program', 'DESC')->get();
        }

        if ($user) {
            $programs->where('user_id', $user);
        }

        if ($orderByYear) {
            $programs->orderBy('date_program', 'DESC');
        }

        if ($withActivity) {
            $programs->with('activities');
        }

        if ($realizedNull) {
            $programs->whereNotNull('realized');
        }

        // Return response
        return ResponseFormatter::success($programs->paginate($limit), 'Fetch success');
    }

    public function create(CreateProgramRequest $request)
    {
        try {
            // create program
            $program = Program::create([
                'name' => $request->name,
                'date_program' => $request->date_program,
                'budget' => $request->budget,
                'realized' => $request->realized,
                'user_id' => $request->user_id,
            ]);

            if (!$program) {
                throw new Exception('Program not created');
            }
    
            return ResponseFormatter::success($program, 'Program Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateProgramRequest $request, $id)
    {
        try {
            // Get program
            $program = Program::find($id);

            // Check if program exists
            if (!$program) {
                throw new Exception('Program not found');
            }
    
            // update program
            $program->update([
                'name' => $request->name,
                'date_program' => $request->date_program,
                'budget' => $request->budget,
                'realized' => $request->realized,
                'user_id' => $request->user_id,
            ]);

            if (!$program) {
                throw new Exception('Program not Updated');
            }
    
            return ResponseFormatter::success($program, 'Program Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function realization(RealizationProgramRequest $request, $id)
    {
        try {
            // Get program
            $program = Program::find($id);

            // Check if program exists
            if (!$program) {
                throw new Exception('Program not found');
            }
    
            // update program
            $program->update([
                'realized' => $request->realized,
            ]);

            if (!$program) {
                throw new Exception('Program not Updated');
            }
    
            return ResponseFormatter::success($program, 'Program Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Get program
            $program = Program::find($id);

            // Check if program exists
            if (!$program) {
                throw new Exception('Program not found');
            }

            // Delete program
            $program->delete();

            return ResponseFormatter::success('Program Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
