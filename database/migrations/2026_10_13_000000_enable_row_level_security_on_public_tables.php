<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Close the Supabase REST roles off from Laravel's public schema tables.
     * Laravel connects as the table owner with BYPASSRLS; the app keeps using
     * its server-side authorization while anon/authenticated API access fails closed.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'borrow_requests',
            'cache',
            'cache_locks',
            'categories',
            'failed_jobs',
            'items',
            'job_batches',
            'jobs',
            'migrations',
            'password_reset_tokens',
            'ratings',
            'reports',
            'sessions',
            'transactions',
            'users',
        ];

        $roles = DB::table('pg_roles')
            ->whereIn('rolname', ['anon', 'authenticated'])
            ->pluck('rolname')
            ->all();

        foreach ($tables as $table) {
            DB::statement('ALTER TABLE public."'.$table.'" ENABLE ROW LEVEL SECURITY');

            foreach ($roles as $role) {
                DB::statement('REVOKE ALL PRIVILEGES ON TABLE public."'.$table.'" FROM "'.$role.'"');
            }
        }

        if ($roles !== []) {
            $quotedRoles = implode(', ', array_map(fn (string $role) => '"'.$role.'"', $roles));
            DB::statement('ALTER DEFAULT PRIVILEGES IN SCHEMA public REVOKE ALL ON TABLES FROM '.$quotedRoles);
        }
    }

    /**
     * Deliberately keep RLS enabled and API grants revoked when rolling back.
     * Reverting to the prior public write/TRUNCATE grants would reopen exposure.
     */
    public function down(): void
    {
        // Security lockdown is intentionally irreversible.
    }
};