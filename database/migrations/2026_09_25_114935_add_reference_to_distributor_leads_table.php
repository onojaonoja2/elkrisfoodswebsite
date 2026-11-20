<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distributor_leads', function (Blueprint $table) {
            $table->string('reference')->nullable()->unique()->after('id');
        });

        DB::table('distributor_leads')
            ->whereNull('reference')
            ->orderBy('id')
            ->pluck('id')
            ->each(function (int $id) {
                DB::table('distributor_leads')
                    ->where('id', $id)
                    ->update(['reference' => 'DST-'.strtoupper(substr(md5('elkris'.$id), 0, 6))]);
            });
    }

    public function down(): void
    {
        Schema::table('distributor_leads', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropColumn('reference');
        });
    }
};
