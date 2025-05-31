<?php

use App\Models\User;
use App\Models\Salary;
use App\Models\CategoryIncome;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Salary Feature Test', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->category = CategoryIncome::factory()->create([
            'users_id' => $this->user->id,
            'name_category_incomes' => 'Gaji Pokok'
        ]);
    });

    it('can store salary via POST', function () {
        $response = $this->postJson(route('admin.income.store'), [
            'salary' => 'Rp. 1.500.000',
            'date' => now()->toDateString(),
            'tipe' => $this->category->id,
            'description' => 'Gaji pokok bulan ini'
        ]);

        $response->assertStatus(200)->assertJson([
            'status' => true,
            'message' => 'Data Created Successfully'
        ]);

        $this->assertDatabaseHas('salaries', [
            'users_id' => $this->user->id,
            'salary' => 1500000,
            'description' => 'Gaji pokok bulan ini'
        ]);
    });

    it('can view salary index page', function () {
        $response = $this->get(route('admin.income.index'));
        $response->assertStatus(200);
    });

    it('can view create form', function () {
        $response = $this->get(route('admin.income.create'));
        $response->assertStatus(200);
    });

    it('can show salary data via AJAX', function () {
        $salary = Salary::factory()->create(['users_id' => $this->user->id]);

        $response = $this->getJson(route('admin.income.show', ['id' => $salary->id]));

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $salary->id]);
    });

    it('can view edit form', function () {
        $salary = Salary::factory()->create(['users_id' => $this->user->id]);

        $response = $this->get(route('admin.income.edit', $salary->id));
        $response->assertStatus(200);
    });

    it('can update salary data', function () {
        $salary = Salary::factory()->create(['users_id' => $this->user->id]);

        $response = $this->putJson(route('admin.income.update', $salary->id), [
            'salary' => 'Rp. 2.000.000',
            'date' => now()->toDateString(),
            'tipe' => $this->category->id,
            'description' => 'Update gaji bulanan'
        ]);

        $response->assertStatus(200)->assertJson([
            'status' => true,
            'message' => 'Data Updated Successfully'
        ]);

        $this->assertDatabaseHas('salaries', [
            'id' => $salary->id,
            'salary' => 2000000,
            'description' => 'Update gaji bulanan'
        ]);
    });

    it('can delete salary data', function () {
        $salary = Salary::factory()->create(['users_id' => $this->user->id]);

        $response = $this->deleteJson(route('admin.income.destroy'), ['id' => $salary->id]);

        $response->assertStatus(200)->assertJson([
            'code' => 200,
            'message' => 'Data successfully deleted'
        ]);

        $this->assertSoftDeleted('salaries', [
            'id' => $salary->id,
            'users_id' => $this->user->id,
        ]);
    });
});
