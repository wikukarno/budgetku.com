<?php

namespace App\Services\Admin;

use App\Repositories\Admin\SalaryRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class SalaryService
{
    protected $salaryRepo;

    public function __construct(SalaryRepository $salaryRepo)
    {
        $this->salaryRepo = $salaryRepo;
    }

    public function getDatatableQuery()
    {
        return $this->salaryRepo->getByUserId(Auth::id());
    }

    public function create(array $request)
    {
        $request['users_uuid'] = Auth::id();

        $salary = $this->formatSalaryForStorage($request['salary']);
        if ((int)$salary <= 0) {
            throw new Exception('Salary must be greater than 0.');
        }
        $request['salary'] = $salary;

        return $this->salaryRepo->create($request);
    }

    public function getById($id)
    {
        return $this->salaryRepo->findById($id);
    }

    public function getByUser($id)
    {
        return $this->salaryRepo->findOrFailByUser($id, Auth::id());
    }

    public function update($id, array $request)
    {
        $salary = $this->salaryRepo->findById($id);

        if (!$salary) {
            throw new Exception('Salary not found.');
        }

        if ($request['date'] > Carbon::now()->format('Y-m-d')) {
            throw new Exception('Tanggal tidak boleh melebihi hari ini.');
        }

        $parsedSalary = $this->formatSalaryForStorage($request['salary']);
        if ((int)$parsedSalary <= 0) {
            throw new Exception('Salary must be greater than 0.');
        }

        $request['users_uuid'] = Auth::id();
        $request['salary'] = $parsedSalary;

        $this->salaryRepo->update($salary, $request);

        return $salary;
    }

    public function delete($id)
    {
        $salary = $this->salaryRepo->findById($id);

        if (!$salary) {
            throw new Exception('Salary not found.');
        }

        return $this->salaryRepo->delete($salary);
    }

    private function formatSalaryForStorage(string $salary): string
    {
        // Remove currency prefix and handle both Indonesian and international formats
        $cleaned = str_replace(['Rp. ', 'Rp ', 'IDR ', '$'], '', $salary);
        $cleaned = trim($cleaned);
        
        // Indonesian format detection:
        // - If comma has 1-2 digits after it, it's decimal (123.456,50)
        // - If comma has 3+ digits after it, it's likely thousand separator error (699,115)
        if (strpos($cleaned, ',') !== false) {
            $parts = explode(',', $cleaned);
            $afterComma = $parts[1] ?? '';
            
            if (strlen($afterComma) <= 2) {
                // Decimal separator: 123.456,50 -> 123456
                $integerPart = str_replace('.', '', $parts[0]);
                $cleaned = $integerPart;
            } else {
                // Likely thousand separator error: 699,115 -> 699115
                $cleaned = str_replace(['.', ','], '', $cleaned);
            }
        } else {
            // No comma, remove dots (thousand separators): 123.456 -> 123456
            $cleaned = str_replace('.', '', $cleaned);
        }
        
        // Return clean integer string
        return preg_replace('/[^0-9]/', '', $cleaned);
    }
}
