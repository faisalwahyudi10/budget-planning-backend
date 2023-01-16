<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProgramRequest;
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

        // Get program
        $programQuery = Program::with('user.employee');

        if ($id) {
            $program = $programQuery->with(['user', 'activities'])->find($id);

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
            $programs->whereYear('date_program', $date);
        }

        if ($user) {
            $programs->where('user_id', $user);
        }

        if ($withActivity) {
            $programs->with('activities');
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
