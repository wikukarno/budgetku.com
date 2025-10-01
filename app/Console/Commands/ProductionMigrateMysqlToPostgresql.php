<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductionMigrateMysqlToPostgresql extends Command
{
    protected $signature = 'migrate:production-mysql-to-pgsql
                            {--backup-only : Only create backup, no migration}
                            {--validate-only : Only validate data consistency}
                            {--force : Skip confirmations}
                            {--chunk-size=1000 : Process records in chunks}';

    protected $description = 'Production-safe migration from MySQL to PostgreSQL with validations';

    private $startTime;
    private $errors = [];
    private $stats = [];

    public function handle()
    {
        $this->startTime = now();

        if (!$this->option('force')) {
            if (!$this->confirmProduction()) {
                $this->error('Migration cancelled by user.');
                return 1;
            }
        }

        try {
            $this->info('🚀 Starting PRODUCTION migration from MySQL to PostgreSQL...');
            $this->info('Started at: ' . $this->startTime);

            // Step 1: Environment validation
            $this->validateEnvironment();

            // Step 2: Create backup
            $backupFile = $this->createBackup();

            if ($this->option('backup-only')) {
                $this->info("✅ Backup completed: {$backupFile}");
                return 0;
            }

            // Step 3: Validate connections
            $this->validateConnections();

            // Step 4: Pre-migration validation
            $this->validateDataConsistency('pre');

            // Step 5: Execute migration
            $this->executeMigration();

            // Step 6: Post-migration validation
            $this->validateDataConsistency('post');

            // Step 7: Performance check
            $this->performanceCheck();

            $this->info('🎉 Production migration completed successfully!');
            $this->displayStats();

            return 0;

        } catch (Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            Log::error('Production Migration Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'stats' => $this->stats
            ]);

            $this->error('Check logs for detailed error information.');
            $this->suggestRollback();

            return 1;
        }
    }

    private function confirmProduction(): bool
    {
        $this->warn('⚠️  PRODUCTION MIGRATION WARNING');
        $this->warn('This will migrate your production data from MySQL to PostgreSQL.');
        $this->warn('Make sure you have:');
        $this->warn('1. Full database backup');
        $this->warn('2. Maintenance window scheduled');
        $this->warn('3. Rollback plan ready');
        $this->warn('4. Team standing by');

        return $this->confirm('Do you want to continue with PRODUCTION migration?');
    }

    private function validateEnvironment(): void
    {
        $this->info('Validating production environment...');

        // Check if we're in production
        if (config('app.env') !== 'production') {
            $this->warn('Not running in production environment: ' . config('app.env'));
        }

        // Check available disk space
        $diskFree = disk_free_space('/');
        $diskTotal = disk_total_space('/');
        $diskUsage = (($diskTotal - $diskFree) / $diskTotal) * 100;

        if ($diskUsage > 80) {
            throw new Exception('Insufficient disk space: ' . round($diskUsage, 2) . '% used');
        }

        // Check memory
        $memoryLimit = ini_get('memory_limit');
        $this->info("PHP Memory Limit: {$memoryLimit}");

        // Check required extensions
        if (!extension_loaded('pdo_pgsql')) {
            throw new Exception('PostgreSQL PDO extension not installed');
        }

        $this->info('✅ Environment validation passed');
    }

    private function createBackup(): string
    {
        $this->info('Creating production backup...');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = "production_backup_mysql_{$timestamp}.sql";

        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $command = "mysqldump -h{$dbHost} -P{$dbPort} -u{$dbUser} -p'{$dbPass}' " .
                  "--single-transaction --routines --triggers --add-drop-table " .
                  "--add-locks --create-options --disable-keys --extended-insert " .
                  "{$dbName} > {$backupFile}";

        exec($command, $output, $return);

        if ($return !== 0) {
            throw new Exception('Backup creation failed');
        }

        $backupSize = filesize($backupFile);
        $this->info("✅ Backup created: {$backupFile} (" . $this->formatBytes($backupSize) . ")");

        return $backupFile;
    }

    private function validateConnections(): void
    {
        $this->info('Validating database connections...');

        try {
            // Test MySQL connection
            DB::connection('mysql')->getPdo();
            $mysqlVersion = DB::connection('mysql')->selectOne('SELECT VERSION() as version');
            $this->info("✅ MySQL connected: " . $mysqlVersion->version);

            // Test PostgreSQL connection
            DB::connection('pgsql')->getPdo();
            $pgsqlVersion = DB::connection('pgsql')->selectOne('SELECT version()');
            $this->info("✅ PostgreSQL connected: " . substr($pgsqlVersion->version, 0, 50) . '...');

        } catch (Exception $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    private function validateDataConsistency(string $phase): void
    {
        $this->info("Validating data consistency ({$phase}-migration)...");

        $mysqlCounts = [
            'users' => DB::connection('mysql')->table('users')->count(),
            'finances' => DB::connection('mysql')->table('finances')->count(),
            'salaries' => DB::connection('mysql')->table('salaries')->count(),
            'category_finances' => DB::connection('mysql')->table('category_finances')->count(),
            'category_incomes' => DB::connection('mysql')->table('category_incomes')->count(),
            'payment_methods' => DB::connection('mysql')->table('payment_methods')->count(),
        ];

        if ($phase === 'post') {
            $pgsqlCounts = [
                'users' => DB::connection('pgsql')->table('users')->count(),
                'finances' => DB::connection('pgsql')->table('finances')->count(),
                'salaries' => DB::connection('pgsql')->table('salaries')->count(),
                'category_finances' => DB::connection('pgsql')->table('category_finances')->count(),
                'category_incomes' => DB::connection('pgsql')->table('category_incomes')->count(),
                'payment_methods' => DB::connection('pgsql')->table('payment_methods')->count(),
            ];

            foreach ($mysqlCounts as $table => $mysqlCount) {
                $pgsqlCount = $pgsqlCounts[$table] ?? 0;
                if ($mysqlCount !== $pgsqlCount) {
                    throw new Exception("Data mismatch in {$table}: MySQL({$mysqlCount}) != PostgreSQL({$pgsqlCount})");
                }
            }

            $this->info('✅ Data consistency validation passed');
        }

        $this->stats["{$phase}_counts"] = $mysqlCounts;
    }

    private function executeMigration(): void
    {
        $this->info('Executing production data migration...');

        $chunkSize = $this->option('chunk-size');

        // Migration with progress tracking
        $this->migrateUsersChunked($chunkSize);
        $this->migrateCategoryFinancesChunked($chunkSize);
        $this->migrateCategoryIncomesChunked($chunkSize);
        $this->migratePaymentMethodsChunked($chunkSize);
        $this->migrateFinancesChunked($chunkSize);
        $this->migrateSalariesChunked($chunkSize);
    }

    private function migrateUsersChunked(int $chunkSize): void
    {
        $this->info('Migrating users...');

        $total = DB::connection('mysql')->table('users')->count();
        DB::connection('pgsql')->table('users')->truncate();

        $bar = $this->output->createProgressBar($total);
        $migrated = 0;

        DB::connection('mysql')->table('users')->chunk($chunkSize, function ($users) use ($bar, &$migrated) {
            foreach ($users as $user) {
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
                $bar->advance();
                $migrated++;
            }
        });

        $bar->finish();
        $this->newLine();
        $this->stats['users_migrated'] = $migrated;
    }

    // Similar chunked methods for other tables...
    private function migrateCategoryFinancesChunked(int $chunkSize): void
    {
        $this->info('Migrating category finances...');
        $total = DB::connection('mysql')->table('category_finances')->count();
        DB::connection('pgsql')->table('category_finances')->truncate();

        $bar = $this->output->createProgressBar($total);
        $migrated = 0;

        DB::connection('mysql')->table('category_finances')->chunk($chunkSize, function ($categories) use ($bar, &$migrated) {
            foreach ($categories as $category) {
                DB::connection('pgsql')->table('category_finances')->insert([
                    'uuid' => $category->uuid,
                    'users_uuid' => $category->users_uuid,
                    'users_id' => $category->users_id,
                    'name_category_finances' => $category->name_category_finances,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                    'deleted_at' => $category->deleted_at,
                ]);
                $bar->advance();
                $migrated++;
            }
        });

        $bar->finish();
        $this->newLine();
        $this->stats['category_finances_migrated'] = $migrated;
    }

    // Add other chunked migration methods...

    private function performanceCheck(): void
    {
        $this->info('Running performance checks...');

        // Test query performance
        $start = microtime(true);
        DB::connection('pgsql')->table('users')->count();
        $userQueryTime = microtime(true) - $start;

        $start = microtime(true);
        DB::connection('pgsql')->table('finances')->count();
        $financeQueryTime = microtime(true) - $start;

        $this->info("✅ Query performance - Users: " . round($userQueryTime * 1000, 2) . "ms, Finances: " . round($financeQueryTime * 1000, 2) . "ms");

        $this->stats['performance'] = [
            'user_query_ms' => round($userQueryTime * 1000, 2),
            'finance_query_ms' => round($financeQueryTime * 1000, 2),
        ];
    }

    private function displayStats(): void
    {
        $duration = now()->diffInSeconds($this->startTime);

        $this->info('');
        $this->info('📊 Migration Statistics:');
        $this->info("Duration: {$duration} seconds");
        foreach ($this->stats as $key => $value) {
            if (is_array($value)) {
                $this->info("{$key}: " . json_encode($value));
            } else {
                $this->info("{$key}: {$value}");
            }
        }
    }

    private function suggestRollback(): void
    {
        $this->error('');
        $this->error('🔄 ROLLBACK INSTRUCTIONS:');
        $this->error('1. Switch .env back to MySQL: DB_CONNECTION=mysql');
        $this->error('2. Restart application');
        $this->error('3. Restore from backup if needed');
        $this->error('4. Check application functionality');
    }

    private function formatBytes(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}
