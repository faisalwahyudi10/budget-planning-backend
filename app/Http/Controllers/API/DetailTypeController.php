<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDetailTypeRequest;
use App\Http\Requests\UpdateDetailTypeRequest;
use App\Models\DetailType;
use Illuminate\Http\Request;
use Exception;

class DetailTypeController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 10);
        $name = $request->input('name');
        // Get detailType
        $detailTypeQuery = DetailType::query();

        if ($id) {
            $detailType = $detailTypeQuery->find($id);

            if ($detailType) {
                return ResponseFormatter::success($detailType, 'Detail Type found');
            }

            return ResponseFormatter::error('Detail Type not found', 404);
        }

        $detailTypes = $detailTypeQuery;

        if ($name) {
            $detailTypes->where('name', $name);
        }

        // Return response
        return ResponseFormatter::success($detailTypes->paginate($limit), 'Fetch success');
    }

    public function create(CreateDetailTypeRequest $request)
    {
        try {
            // create Detail Type
            $detailType = DetailType::create([
                'name' => $request->name,
            ]);

            if (!$detailType) {
                throw new Exception('Detail Type not created');
            }
    
            return ResponseFormatter::success($detailType, 'Detail Type Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateDetailTypeRequest $request, $id)
    {
        try {
            // Get detailType
            $detailType = DetailType::find($id);

            // Check if detailType exists
            if (!$detailType) {
                throw new Exception('Detail Type not found');
            }
            // update employee
            $detailType->update([
                'name' => $request->name,
            ]);

            if (!$detailType) {
                throw new Exception('Detail Type not Updated');
            }
    
            return ResponseFormatter::success($detailType, 'Detail Type Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Get detail type
            $detailType = DetailType::find($id);
            

            // Check if DetailType exists
            if (!$detailType) {
                throw new Exception('Detail Type not found');
            }

            if ($detailType->is_verified == 1) {
                throw new Exception('Detail Type is Active');
            }

            // Delete DetailType
            $detailType->delete();

            return ResponseFormatter::success('Detail Type Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
