<?php

use Illuminate\Database\Seeder;
use App\MaintenanceItem;

class MaintenanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row = 5;
        $col = 4;
        DB::table('maintenance')->insert([
            'area_code' => '321',
            'description' => 'West Wing',
            'floor' => 'Ground',
            'row' => $row,
            'column' => $col,
        ]);
        
        for ($r=1;$r<=$row;$r++) {
            for($c=1;$c<=$col;$c++){
                $description = 'R'.$r.'C'.$c;
                $maintenance_items[] = array(
                    'description'=> $description,
                    'maintenance_id'=> 1,
                    'table_status_id'=> '2',
                    'row_position'=> $r,
                    'col_position'=> $c,
                );
            }
        }

        MaintenanceItem::insert($maintenance_items);

        $row = 2;
        $col = 2;
        DB::table('maintenance')->insert([
            'area_code' => '567',
            'description' => 'Front',
            'floor' => 'Ground',
            'row' => $row,
            'column' => $col,
        ]);
        
        for ($r=1;$r<=$row;$r++) {
            for($c=1;$c<=$col;$c++){
                $description = 'R'.$r.'C'.$c;
                $maintenance_items[] = array(
                    'description'=> $description,
                    'maintenance_id'=> 2,
                    'table_status_id'=> '2',
                    'row_position'=> $r,
                    'col_position'=> $c,
                );
            }
        }

        MaintenanceItem::insert($maintenance_items);
    }
}
