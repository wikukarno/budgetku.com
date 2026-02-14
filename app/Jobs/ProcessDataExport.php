<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\CategoryFinance;
use App\Models\CategoryIncome;
use App\Models\DataExport;
use App\Models\Finance;
use App\Models\PaymentMethod;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ProcessDataExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 1;

    public function __construct(
        protected string $exportUuid,
        protected string $userUuid
    ) {}

    public function handle(): void
    {
        $export = DataExport::find($this->exportUuid);
        if (!$export) {
            return;
        }

        $export->update(['status' => 'processing']);

        try {
            $user = User::where('uuid', $this->userUuid)->firstOrFail();
            $this->buildZip($export, $user);
        } catch (\Throwable $e) {
            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    protected function buildZip(DataExport $export, User $user): void
    {
        $exportDir = 'exports';
        if (!Storage::disk('local')->exists($exportDir)) {
            Storage::disk('local')->makeDirectory($exportDir);
        }

        $userName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name);
        $fileName = "budgetku-data-{$userName}-" . now()->format('Y-m-d') . ".zip";
        $filePath = "exports/{$export->uuid}.zip";
        $fullPath = storage_path("app/{$filePath}");

        $zip = new ZipArchive();
        if ($zip->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create ZIP file.');
        }

        // 1. Profile
        $profile = [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles,
            'saldo' => $user->saldo,
            'telegram_username' => $user->telegram_username,
            'created_at' => $user->created_at?->toDateTimeString(),
            'last_login_at' => $user->last_login_at,
        ];
        $zip->addFromString('data/profile.json', json_encode($profile, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 2. Expense categories
        $expenseCategories = CategoryFinance::where('users_uuid', $user->uuid)
            ->get(['uuid', 'name_category_finances', 'created_at']);
        $zip->addFromString('data/expense_categories.json', $expenseCategories->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 3. Income categories
        $incomeCategories = CategoryIncome::where('users_uuid', $user->uuid)
            ->get(['uuid', 'name_category_incomes', 'created_at']);
        $zip->addFromString('data/income_categories.json', $incomeCategories->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 4. Payment methods
        $paymentMethods = PaymentMethod::where('users_uuid', $user->uuid)
            ->get(['uuid', 'name', 'icon', 'created_at']);
        $zip->addFromString('data/payment_methods.json', $paymentMethods->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 5. Expenses (chunked to handle large datasets)
        $expensesData = collect();
        $expenseImages = [];

        Finance::where('users_uuid', $user->uuid)
            ->with(['category_finance:uuid,name_category_finances'])
            ->chunk(500, function ($expenses) use ($paymentMethods, &$expensesData, &$expenseImages) {
                foreach ($expenses as $expense) {
                    $paymentMethod = $paymentMethods->firstWhere('uuid', $expense->payment_methods_uuid);

                    $expensesData->push([
                        'uuid' => $expense->uuid,
                        'name_item' => $expense->name_item,
                        'price' => $expense->price,
                        'purchase_date' => $expense->purchase_date,
                        'category' => $expense->category_finance?->name_category_finances,
                        'payment_method' => $paymentMethod?->name,
                        'bukti_pembayaran' => $expense->bukti_pembayaran ? basename($expense->bukti_pembayaran) : null,
                        'created_at' => $expense->created_at?->toDateTimeString(),
                    ]);

                    if ($expense->bukti_pembayaran) {
                        $expenseImages[] = $expense->bukti_pembayaran;
                    }
                }
            });

        $zip->addFromString('data/expenses.json', $expensesData->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 6. Incomes (chunked)
        $incomesData = collect();

        Salary::where('users_uuid', $user->uuid)
            ->with(['category_income:uuid,name_category_incomes'])
            ->chunk(500, function ($incomes) use (&$incomesData) {
                foreach ($incomes as $income) {
                    $incomesData->push([
                        'uuid' => $income->uuid,
                        'salary' => $income->salary,
                        'date' => $income->date,
                        'description' => $income->description,
                        'category' => $income->category_income?->name_category_incomes,
                        'created_at' => $income->created_at?->toDateTimeString(),
                    ]);
                }
            });

        $zip->addFromString('data/incomes.json', $incomesData->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 7. Bills
        $bills = Bill::where('users_id', $user->id)->get();
        $zip->addFromString('data/bills.json', $bills->makeHidden(['id', 'users_id', 'deleted_at', 'updated_at'])->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 8. Avatar image
        if ($user->avatar) {
            $this->addImageToZip($zip, $user->avatar, 'images/avatar');
        }

        // 9. Bukti pembayaran images
        foreach ($expenseImages as $imagePath) {
            $this->addImageToZip($zip, $imagePath, 'images/bukti_pembayaran');
        }

        $zip->close();

        $fileSize = filesize($fullPath);

        $export->update([
            'status' => 'completed',
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'completed_at' => now(),
            'expires_at' => now()->addHours(24),
        ]);
    }

    protected function addImageToZip(ZipArchive $zip, string $imagePath, string $zipFolder): void
    {
        $storagePath = str_replace('/storage/', '', $imagePath);

        if (Storage::disk('public')->exists($storagePath)) {
            $zip->addFromString(
                $zipFolder . '/' . basename($storagePath),
                Storage::disk('public')->get($storagePath)
            );
        }
    }
}
