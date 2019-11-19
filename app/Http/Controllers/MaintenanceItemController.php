<?php

namespace App\Http\Controllers;
use App\MaintenanceItem;
use App\TableStatus;

use Illuminate\Http\Request;

class MaintenanceItemController extends Controller
{
    public function index() {
        $id = request()->id;
        $maintenance_items = MaintenanceItem::with('tablestatus')->where('maintenance_id', $id)->get();
        $table_status = TableStatus::get();
        $title = 'Maintenance Items';

        return view('maintenance-items', compact('maintenance_items','table_status', 'title', 'id'));
    }
}
