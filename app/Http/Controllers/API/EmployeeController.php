<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeStatusRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Exception;

class EmployeeController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 10);
        $name = $request->input('name');
        $nik = $request->input('nik');
        $position = $request->input('position');
        $isVerified = $request->input('is_verified', false);
        // Get employee
        $employeeQuery = Employee::query();

        if ($id) {
            $employee = $employeeQuery->find($id);

            if ($employee) {
                return ResponseFormatter::success($employee, 'Employee found');
            }

            return ResponseFormatter::error('Employee not found', 404);
        }

        $employees = $employeeQuery;

        if ($name) {
            $employees->where('name', 'like', '%' . $name . '%');
        }
        
        if ($nik) {
            $employees->where('nik', $nik);
        }

        if ($position) {
            $employees->where('position', $position);
        }

        if ($isVerified) {
            $employees->where('is_verified', $isVerified);
        }

        // Return response
        return ResponseFormatter::success($employees->paginate($limit), 'Fetch success');
    }

    public function create(CreateEmployeeRequest $request)
    {
        try {
            // Upload photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/userPhotos');
            }
    
            // create employee
            $employee = Employee::create([
                'name' => $request->name,
                'nik' => $request->nik,
                'position' => $request->position,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : 'public/userPhotos/cover-not-found.jpg',
                'email' => $request->email,
                'address' => $request->address,
            ]);

            if (!$employee) {
                throw new Exception('Employee not created');
            }
    
            return ResponseFormatter::success($employee, 'Employee Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            // Get employee
            $employee = Employee::find($id);

            // Check if employee exists
            if (!$employee) {
                throw new Exception('Employee not found');
            }
            // Upload photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/userPhotos');
            }
    
            // update employee
            $employee->update([
                'name' => $request->name,
                'nik' => $request->nik,
                'position' => $request->position,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : $employee->photo,
                'email' => $request->email,
                'address' => $request->address,
            ]);

            if (!$employee) {
                throw new Exception('Employee not Updated');
            }
    
            return ResponseFormatter::success($employee, 'Employee Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function updateStatus(UpdateEmployeeStatusRequest $request, $id)
    {
        try {
            // Get Employee
            $employee = Employee::find($id);
            
            // Check if Employee exists
            if (!$employee) {
                throw new Exception('Employee not found');
            }

            // Update employee
            $employee->update([
                'is_verified' => $request->is_verified,
            ]);
    
            return ResponseFormatter::success($employee, 'Employee Status Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Get employee
            $employee = Employee::find($id);

            // Check if employee exists
            if (!$employee) {
                throw new Exception('Employee not found');
            }

            if ($employee->is_verified == 1) {
                throw new Exception('Employee is Active');
            }

            // Delete employee
            $employee->delete();

            return ResponseFormatter::success('Employee Deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
