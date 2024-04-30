<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Pass;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $passes = Pass::factory()
            ->count(2)
            ->state(['authentication_token' => 'authenticated'])
            ->sequence(
                ['serial_number' => 'bar'],
                ['serial_number' => 'baz']
            )->create();

        Device::factory(1, [
            'device_library_identifier' => 'foo',
            'push_token' => 'push_token',
        ])->hasAttached($passes)->create();

        // Random data
        Device::factory()->count(9)
            ->hasPasses(3)
            ->create();
    }
}
