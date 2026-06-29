<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 100)->nullable()->after('full_name');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'password_hash')) {
                $table->string('password_hash')->nullable()->after('password');
            }

            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('role')->index();
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role_id');
            }
        });

        // Backfill supaya data lama langsung kompatibel dengan UI/admin query.
        DB::table('users')
            ->whereNull('full_name')
            ->update(['full_name' => DB::raw('name')]);

        DB::table('users')
            ->whereNull('password_hash')
            ->update(['password_hash' => DB::raw('password')]);

        // Generate username unik sederhana dari email (bagian sebelum @).
        $users = DB::table('users')->select('id', 'email', 'username')->orderBy('id')->get();
        $used = [];

        foreach ($users as $user) {
            $current = trim((string) ($user->username ?? ''));
            if ($current !== '') {
                $used[strtolower($current)] = true;
                continue;
            }

            $base = strtolower((string) strstr((string) $user->email, '@', true));
            $base = preg_replace('/[^a-z0-9_]/', '', $base ?? '');
            if ($base === '' || $base === null) {
                $base = 'user';
            }

            $candidate = $base;
            $i = 1;
            while (isset($used[strtolower($candidate)])) {
                $candidate = $base . $i;
                $i++;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $candidate]);
            $used[strtolower($candidate)] = true;
        }

        // Samakan role_id dengan role string agar query admin lama tetap jalan.
        DB::table('users')
            ->whereRaw('LOWER(COALESCE(role, "")) in ("admin","superadmin")')
            ->update(['role_id' => 1]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropColumn('role_id');
            }
            if (Schema::hasColumn('users', 'password_hash')) {
                $table->dropColumn('password_hash');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('users', 'full_name')) {
                $table->dropColumn('full_name');
            }
        });
    }
};
