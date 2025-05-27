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

        $salary = (int) preg_replace('/[^0-9]/', '', $request['salary']);
        if ($salary <= 0) {
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

        $parsedSalary = (int) preg_replace('/[^0-9]/', '', $request['salary']);
        if ($parsedSalary <= 0) {
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
}
