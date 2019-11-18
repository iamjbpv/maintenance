<?php

use Illuminate\Database\Seeder;

class TableStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('table_status')->insert([
            'name' => 'Reserved'
        ]);

        DB::table('table_status')->insert([
            'name' => 'Available'
        ]);
        
        DB::table('table_status')->insert([
            'name' => 'Unavailable'
        ]);

        DB::table('table_status')->insert([
            'name' => 'Occupied'
        ]);
    }
}
