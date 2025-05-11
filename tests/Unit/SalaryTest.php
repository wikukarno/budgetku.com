<?php

use App\Models\Salary;
use App\Services\Admin\SalaryService;
use App\Repositories\Admin\SalaryRepository;
use Illuminate\Support\Facades\Auth;
use Mockery;

describe('SalaryService (unit test with mocking)', function () {
    beforeEach(function () {
        $this->user = (object) ['id' => 1];
        Auth::shouldReceive('id')->andReturn($this->user->id);

        $this->mockRepo = Mockery::mock(SalaryRepository::class);
        $this->service = new SalaryService($this->mockRepo);
    });

    it('can create salary data', function () {
        $input = [
            'salary' => 'Rp. 1.000.000',
            'date' => '2024-01-01',
            'tipe' => 'Gaji Bulanan',
            'description' => 'Gaji bulan ini'
        ];
        
        $this->mockRepo
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg['users_id'] === 1 && $arg['salary'] === 1000000;
            }))
            ->andReturn(new Salary([
                'users_id' => 1,
                'salary' => 1000000,
                'date' => '2024-01-01',
                'tipe' => 'Gaji Bulanan',
                'description' => 'Gaji bulan ini'
            ]));

        $salary = $this->service->create($input);

        expect($salary)->toBeInstanceOf(Salary::class)
            ->and($salary->salary)->toEqual(1000000);
    });

    it('throws exception if date is in the future on update', function () {
        $salary = new Salary(['id' => 1]);

        $this->mockRepo->shouldReceive('findById')->andReturn($salary);

        $data = [
            'salary' => 'Rp. 500.000',
            'date' => now()->addDay()->toDateString(),
            'tipe' => 'Bonus',
            'description' => 'Bonus bulan depan'
        ];

        $this->expectException(Exception::class);
        $this->service->update(1, $data);
    });

    it('can update salary data', function () {
        $salary = new Salary(['id' => 1]);

        $this->mockRepo->shouldReceive('findById')->andReturn($salary);
        $this->mockRepo->shouldReceive('update')->once()->andReturnUsing(function ($salary, $data) {
            $salary->salary = $data['salary'];
            $salary->description = $data['description'];
            return $salary;
        });

        $data = [
            'salary' => 'Rp. 750.000',
            'date' => now()->toDateString(),
            'tipe' => 'Insentif',
            'description' => 'Insentif bulanan'
        ];

        $updated = $this->service->update(1, $data);

        expect($updated->salary)->toEqual(750000)
            ->and($updated->description)->toBe('Insentif bulanan');
    });

    it('can delete salary data', function () {
        $salary = new Salary(['id' => 1]);

        $this->mockRepo->shouldReceive('findById')->andReturn($salary);
        $this->mockRepo->shouldReceive('delete')->once()->with($salary)->andReturn(true);

        $result = $this->service->delete(1);

        expect($result)->toBeTrue();
    });

    afterEach(function () {
        Mockery::close();
    });
});
