<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('managed_departments')->nullable()->after('managed_department');
        });

        $users = DB::table('users')
            ->select('id', 'managed_department')
            ->whereNotNull('managed_department')
            ->get();

        foreach ($users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'managed_departments' => json_encode([$user->managed_department], JSON_UNESCAPED_UNICODE),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('managed_departments');
        });
    }
};
