<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class SettlementSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('settlements:import', [
            '--truncate' => true,
        ]);

        $this->command?->line(Artisan::output());
    }
}
