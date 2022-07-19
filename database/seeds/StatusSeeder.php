<?php

use App\Status;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::create([
            'name' => 'active'
        ]);
        Status::create([
            'name' => 'inactive'
        ]);
    }
}
