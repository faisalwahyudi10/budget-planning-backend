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
        $withExpenses1 = $request->input('with_expenses_sum1', false);
        $withExpenses2 = $request->input('with_expenses_sum2', false);
        $withExpenses3 = $request->input('with_expenses_sum3', false);
        $withExpenses4 = $request->input('with_expenses_sum4', false);
        $withExpenseP = $request->input('with_expenses1', false);
        $withExpenseR = $request->input('with_expenses2', false);
        $withExpenseN = $request->input('with_expenses3', false);
        $withExpenseE = $request->input('with_expenses4', false);
        $realizedNull = $request->input('realized_null', false);
        $orderByYear = $request->input('order_year', false);
        $groupByYear = $request->input('group_year', false);
        $withSemesterOp1 = $request->input('budget_operation1', false);
        $withSemesterOp2 = $request->input('budget_operation2', false);
        $withSemesterMo3 = $request->input('budget_modal3', false);
        $withSemesterMo4 = $request->input('budget_modal4', false);
        $withRealSemesterOp1 = $request->input('real_operation1', false);
        $withRealSemesterOp2 = $request->input('real_operation2', false);
        $withRealSemesterMo3 = $request->input('real_modal3', false);
        $withRealSemesterMo4 = $request->input('real_modal4', false);
        $withTotalBudget = $request->input('total_budget', false);
        $withTotalReal1 = $request->input('total_real1', false);
        $withTotalReal2 = $request->input('total_real2', false);
        $withGroupByDetailType1 = $request->input('group_detail1', false);
        $withGroupByDetailType2 = $request->input('group_detail2', false);
        $withGroupByDetailType3 = $request->input('group_detail3', false);
        $withGroupByDetailType4 = $request->input('group_detail4', false);
        $withGroupByDetailTypeReal1 = $request->input('group_real_detail1', false);
        $withGroupByDetailTypeReal2 = $request->input('group_real_detail2', false);
        $withGroupByDetailTypeReal3 = $request->input('group_real_detail3', false);
        $withGroupByDetailTypeReal4 = $request->input('group_real_detail4', false);
        $year1 = $request->input('first_year');
        $year2 = $request->input('last_year');

        // Get program
        $programQuery = Program::with('user.employee');

        if ($id) {

            if ($withExpenseP) {
                $program = $programQuery->with(['user', 'activities', 'activities.expenses'])->find($id);
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
                        $query->whereNotNull('realized');
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
                        $query->whereNotNull('realized')
                        ->selectRaw('sum(cost*amount) as jumlah, tw, activity_id')
                        ->groupBy('tw', 'activity_id')
                        ->without('activity');
                    }
                ])->find($id);
            } elseif ($withSemesterOp1) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withSemesterOp2) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withSemesterMo3) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withSemesterMo4) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withRealSemesterOp1) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')->whereIn('tw', [1,2])
                    ->selectRaw('sum(realization*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withRealSemesterOp2) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')->whereIn('tw', [3,4])
                    ->selectRaw('sum(realization*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withRealSemesterMo3) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')->whereIn('tw', [1,2])
                    ->selectRaw('sum(realization*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withRealSemesterMo4) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')->whereIn('tw', [3,4])
                    ->selectRaw('sum(realization*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withTotalBudget) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withTotalReal1) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->whereIn('tw', [1,2])
                    ->selectRaw('sum(realization*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withTotalReal2) {
                $program = $programQuery->with(['activities.expenses' => function ($query) {
                    $query->whereIn('tw', [3,4])
                    ->selectRaw('sum(realization*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->find($id);
            } elseif ($withGroupByDetailType1) {
                $program = $programQuery->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')
                    ->selectRaw('sum(cost*amount) as budget, sum(realized*amount_real) as actual, detailType_id, program_id')
                    ->orderBy('detailType_id')
                    ->groupBy('program_id', 'detailType_id');
                }, 'expenses.detailType'])->find($id);
            } elseif ($withGroupByDetailType2) {
                $program = $programQuery->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')
                    ->selectRaw('sum(cost*amount) as budget, sum(realized*amount_real) as actual, detailType_id, program_id')
                    ->orderBy('detailType_id')
                    ->groupBy('program_id', 'detailType_id');
                }, 'expenses.detailType'])->find($id);
            } elseif ($withGroupByDetailType3) {
                $program = $programQuery->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')
                    ->selectRaw('sum(cost*amount) as budget, sum(realized*amount_real) as actual, detailType_id, program_id')->orderBy('detailType_id')
                    ->groupBy('program_id', 'detailType_id');
                }, 'expenses.detailType'])->find($id);
            } elseif ($withGroupByDetailType4) {
                $program = $programQuery->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')
                    ->selectRaw('sum(cost*amount) as budget, sum(realized*amount_real) as actual, detailType_id, program_id')->orderBy('detailType_id')
                    ->groupBy('program_id', 'detailType_id');
                }, 'expenses.detailType'])->find($id);
            } elseif ($withGroupByDetailTypeReal1) {
                $program = $programQuery->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')->whereIn('tw', [1,2])
                    ->selectRaw('sum(realized*amount_real) as actual, detailType_id, program_id')
                    ->orderBy('detailType_id')
                    ->groupBy('program_id', 'detailType_id');
                }, 'expenses.detailType'])->find($id);
            } elseif ($withGroupByDetailTypeReal2) {
                $program = $programQuery->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')->whereIn('tw', [3,4])
                    ->selectRaw('sum(realized*amount_real) as actual, detailType_id, program_id')
                    ->orderBy('detailType_id')
                    ->groupBy('program_id', 'detailType_id');
                }, 'expenses.detailType'])->find($id);
            } elseif ($withGroupByDetailTypeReal3) {
                $program = $programQuery->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')->whereIn('tw', [1,2])
                    ->selectRaw('sum(realized*amount_real) as actual, detailType_id, program_id')
                    ->orderBy('detailType_id')
                    ->groupBy('program_id', 'detailType_id');
                }, 'expenses.detailType'])->find($id);
            } elseif ($withGroupByDetailTypeReal4) {
                $program = $programQuery->with(['expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')->whereIn('tw', [3,4])
                    ->selectRaw('sum(realized*amount_real) as actual, detailType_id, program_id')
                    ->orderBy('detailType_id')
                    ->groupBy('program_id', 'detailType_id');
                }, 'expenses.detailType'])->find($id);
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

            if ($withSemesterOp1) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withSemesterOp2) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withSemesterMo3) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withSemesterMo4) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }

            if ($withRealSemesterOp1) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')->whereIn('tw', [1,2])
                    ->selectRaw('sum(realized*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withRealSemesterOp2) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Operasi')->whereIn('tw', [3,4])
                    ->selectRaw('sum(realized*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withRealSemesterMo3) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')->whereIn('tw', [1,2])
                    ->selectRaw('sum(realized*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withRealSemesterMo4) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->where('expense_type', 'Modal')->whereIn('tw', [3,4])
                    ->selectRaw('sum(realized*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withTotalBudget) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query
                    ->selectRaw('sum(cost*amount) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withTotalReal1) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->whereIn('tw', [1,2])
                    ->selectRaw('sum(realized*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }
            if ($withTotalReal2) {
                $programs->with(['activities.expenses' => function ($query) {
                    $query->whereIn('tw', [3,4])
                    ->selectRaw('sum(realized*amount_real) as jumlah, activity_id')
                    ->groupBy('activity_id');
                }])->get();
            }


        if ($year1 && $year2) {
            $programs->whereBetween('date_program', [$year1, $year2]);
        }

        if ($user) {
            $programs->where('user_id', $user);
        }

        if ($orderByYear) {
            $programs->orderBy('date_program', 'DESC');
        }

        if ($groupByYear) {
            $programs->selectRaw('sum(realized) as realized, date_program')->groupBy('date_program')->orderBy('date_program', 'DESC')->get();
        }

        if ($withActivity) {
            $programs->with('activities');
        }

        if ($withExpenses1) {
            $programs->with(['expenses' => function ($query) {
                $query->where('expense_type', 'Operasi')
                ->selectRaw('sum(cost*amount) as budget, sum(realized*amount_real) as actual, detailType_id, program_id')->orderBy('detailType_id')
                ->groupBy('program_id', 'detailType_id');
            }, 'expenses.detailType'])->get();
        }

        if ($withExpenses2) {
            $programs->with(['expenses' => function ($query) {
                $query->where('expense_type', 'Operasi')
                ->selectRaw('sum(cost*amount) as budget, sum(realized*amount_real) as actual, detailType_id, program_id')->orderBy('detailType_id')
                ->groupBy('program_id', 'detailType_id');
            }, 'expenses.detailType'])->get();
        }

        if ($withExpenses3) {
            $programs->with(['expenses' => function ($query) {
                $query->where('expense_type', 'Modal')
                ->selectRaw('sum(cost*amount) as budget, sum(realized*amount_real) as actual, detailType_id, program_id')->orderBy('detailType_id')
                ->groupBy('program_id', 'detailType_id');
            }, 'expenses.detailType'])->get();
        }

        if ($withExpenses4) {
            $programs->with(['expenses' => function ($query) {
                $query->where('expense_type', 'Modal')
                ->selectRaw('sum(cost*amount) as budget, sum(realized*amount_real) as actual, detailType_id, program_id')->orderBy('detailType_id')
                ->groupBy('program_id', 'detailType_id');
            }, 'expenses.detailType'])->get();
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
