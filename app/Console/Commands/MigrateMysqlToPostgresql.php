<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Finance;
use App\Models\Salary;
use App\Models\CategoryFinance;
use App\Models\CategoryIncome;
use App\Models\PaymentMethod;

class MigrateMysqlToPostgresql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:mysql-to-pgsql {--dry-run : Show what would be migrated without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from MySQL to PostgreSQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('🚀 Starting migration from MySQL to PostgreSQL...');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual data will be migrated');
        }

        try {
            // Test connections
            $this->info('Testing database connections...');
            DB::connection('mysql')->getPdo();
            DB::connection('pgsql')->getPdo();
            $this->info('✅ Both database connections successful');

            // Migrate tables in proper order (respecting foreign keys)
            $this->migrateUsers($isDryRun);
            $this->migrateCategoryFinances($isDryRun);
            $this->migrateCategoryIncomes($isDryRun);
            $this->migratePaymentMethods($isDryRun);
            $this->migrateFinances($isDryRun);
            $this->migrateSalaries($isDryRun);

            if (!$isDryRun) {
                $this->info('🎉 Migration completed successfully!');
            } else {
                $this->info('🔍 Dry run completed. Use --dry-run=false to perform actual migration.');
            }

        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function migrateUsers($isDryRun)
    {
        $this->info('Migrating Users...');

        $mysqlUsers = DB::connection('mysql')->table('users')->get();
        $this->info("Found {$mysqlUsers->count()} users in MySQL");

        if (!$isDryRun && $mysqlUsers->count() > 0) {
            // Clear existing data in PostgreSQL
            DB::connection('pgsql')->table('users')->truncate();

            foreach ($mysqlUsers as $user) {
                DB::connection('pgsql')->table('users')->insert([
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_parrent' => $user->email_parrent,
                    'password' => $user->password,
                    'roles' => $user->roles,
                    'avatar' => $user->avatar,
                    'saldo' => $user->saldo ?? 0,
                    'two_factor_enabled' => $user->two_factor_enabled ?? false,
                    'two_factor_secret' => $user->two_factor_secret,
                    'two_factor_recovery_codes' => $user->two_factor_recovery_codes,
                    'two_factor_codes_downloaded' => $user->two_factor_codes_downloaded ?? false,
                    'last_login_at' => $user->last_login_at,
                    'last_login_ip' => $user->last_login_ip,
                    'telegram_id' => $user->telegram_id,
                    'telegram_username' => $user->telegram_username,
                    'auth_token' => $user->auth_token,
                    'notifications' => $user->notifications ?? true,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'deleted_at' => $user->deleted_at,
                ]);
            }
        }

        $this->info("✅ Users migration " . ($isDryRun ? 'simulated' : 'completed'));
    }

    private function migrateCategoryFinances($isDryRun)
    {
        $this->info('Migrating Category Finances...');

        $mysqlCategories = DB::connection('mysql')->table('category_finances')->get();
        $this->info("Found {$mysqlCategories->count()} category finances in MySQL");

        if (!$isDryRun && $mysqlCategories->count() > 0) {
            DB::connection('pgsql')->table('category_finances')->truncate();

            foreach ($mysqlCategories as $category) {
                DB::connection('pgsql')->table('category_finances')->insert([
                    'uuid' => $category->uuid,
                    'users_uuid' => $category->users_uuid,
                    'users_id' => $category->users_id,
                    'name_category_finances' => $category->name_category_finances,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                    'deleted_at' => $category->deleted_at,
                ]);
            }
        }

        $this->info("✅ Category Finances migration " . ($isDryRun ? 'simulated' : 'completed'));
    }

    private function migrateCategoryIncomes($isDryRun)
    {
        $this->info('Migrating Category Incomes...');

        $mysqlCategories = DB::connection('mysql')->table('category_incomes')->get();
        $this->info("Found {$mysqlCategories->count()} category incomes in MySQL");

        if (!$isDryRun && $mysqlCategories->count() > 0) {
            DB::connection('pgsql')->table('category_incomes')->truncate();

            foreach ($mysqlCategories as $category) {
                DB::connection('pgsql')->table('category_incomes')->insert([
                    'uuid' => $category->uuid,
                    'users_uuid' => $category->users_uuid,
                    'users_id' => $category->users_id,
                    'name_category_incomes' => $category->name_category_incomes,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                    'deleted_at' => $category->deleted_at,
                ]);
            }
        }

        $this->info("✅ Category Incomes migration " . ($isDryRun ? 'simulated' : 'completed'));
    }

    private function migratePaymentMethods($isDryRun)
    {
        $this->info('Migrating Payment Methods...');

        $mysqlPaymentMethods = DB::connection('mysql')->table('payment_methods')->get();
        $this->info("Found {$mysqlPaymentMethods->count()} payment methods in MySQL");

        if (!$isDryRun && $mysqlPaymentMethods->count() > 0) {
            DB::connection('pgsql')->table('payment_methods')->truncate();

            foreach ($mysqlPaymentMethods as $paymentMethod) {
                DB::connection('pgsql')->table('payment_methods')->insert([
                    'uuid' => $paymentMethod->uuid,
                    'name' => $paymentMethod->name,
                    'icon' => $paymentMethod->icon,
                    'users_id' => $paymentMethod->users_id ?? null,
                    'created_at' => $paymentMethod->created_at,
                    'updated_at' => $paymentMethod->updated_at,
                    'deleted_at' => $paymentMethod->deleted_at,
                ]);
            }
        }

        $this->info("✅ Payment Methods migration " . ($isDryRun ? 'simulated' : 'completed'));
    }

    private function migrateFinances($isDryRun)
    {
        $this->info('Migrating Finances...');

        $mysqlFinances = DB::connection('mysql')->table('finances')->get();
        $this->info("Found {$mysqlFinances->count()} finances in MySQL");

        if (!$isDryRun && $mysqlFinances->count() > 0) {
            DB::connection('pgsql')->table('finances')->truncate();

            foreach ($mysqlFinances as $finance) {
                DB::connection('pgsql')->table('finances')->insert([
                    'uuid' => $finance->uuid,
                    'users_uuid' => $finance->users_uuid,
                    'users_id' => $finance->users_id,
                    'category_finances_uuid' => $finance->category_finances_uuid,
                    'category_finances_id' => $finance->category_finances_id,
                    'name_item' => $finance->name_item,
                    'price' => $finance->price,
                    'purchase_date' => $finance->purchase_date,
                    'purchase_by' => $finance->purchase_by ?? null,
                    'payment_methods_uuid' => $finance->payment_methods_uuid,
                    'bukti_pembayaran' => $finance->bukti_pembayaran,
                    'created_at' => $finance->created_at,
                    'updated_at' => $finance->updated_at,
                    'deleted_at' => $finance->deleted_at,
                ]);
            }
        }

        $this->info("✅ Finances migration " . ($isDryRun ? 'simulated' : 'completed'));
    }

    private function migrateSalaries($isDryRun)
    {
        $this->info('Migrating Salaries...');

        $mysqlSalaries = DB::connection('mysql')->table('salaries')->get();
        $this->info("Found {$mysqlSalaries->count()} salaries in MySQL");

        if (!$isDryRun && $mysqlSalaries->count() > 0) {
            DB::connection('pgsql')->table('salaries')->truncate();

            foreach ($mysqlSalaries as $salary) {
                DB::connection('pgsql')->table('salaries')->insert([
                    'uuid' => $salary->uuid,
                    'users_uuid' => $salary->users_uuid,
                    'category_incomes_uuid' => $salary->category_incomes_uuid,
                    'salary' => $salary->salary,
                    'date' => $salary->date,
                    'description' => $salary->description,
                    'created_at' => $salary->created_at,
                    'updated_at' => $salary->updated_at,
                    'deleted_at' => $salary->deleted_at,
                ]);
            }
        }

        $this->info("✅ Salaries migration " . ($isDryRun ? 'simulated' : 'completed'));
    }
}
