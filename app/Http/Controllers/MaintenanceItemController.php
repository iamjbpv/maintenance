<?php

namespace App\Http\Controllers;
use App\Maintenance;
use App\MaintenanceItem;
use App\TableStatus;

use Illuminate\Http\Request;

class MaintenanceItemController extends Controller
{
    public function index() {
        $id = request()->id;
        $maintenance_desc = Maintenance::where('id', $id)->get(['description']);
        $maintenance_desc = $maintenance_desc[0]->description;
        // $maintenance_items = MaintenanceItem::with('tablestatus')->where('maintenance_id', $id)->get();
        $maintenance_items = [];
        //lets use chunk to handle large amounts of data
        MaintenanceItem::with('tablestatus')->where('maintenance_id',request()->id)->orderBy('id')->chunk(1000, function ($items) use(&$maintenance_items){
            $maintenance_items[] = $items;
        });

        $maintenance_items = $maintenance_items[0];

        $table_status = TableStatus::get();
        $title = 'Maintenance Items';

        return view('maintenance-items', compact('maintenance_items','table_status', 'title', 'id', 'maintenance_desc'));
    }
}
