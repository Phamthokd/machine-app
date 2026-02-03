<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanupRepairTicketsSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('repair_tickets')->truncate();
        
        Schema::enableForeignKeyConstraints();
        
        $this->command->info('All repair tickets have been deleted.');
    }
}
