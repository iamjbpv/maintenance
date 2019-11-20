<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Maintenance;
use App\MaintenanceItem;
use App\TableStatus;

class MaintenanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $maintenance = Maintenance::get();
        $table_status = TableStatus::get();
        $title = 'Maintenance';

    	return view('maintenance', compact('maintenance','table_status', 'title'));
    }

    public function list()
    {
        $maintenance["data"] = Maintenance::get();
        return response()->json($maintenance);
    }

    public function store()
    {
        $id = request()->id;

        $customFieldNames = [
            'area_code' => 'Area Code',
            'description' => 'Description',
            'floor' => 'Floor',
            'row' => 'Row',
            'column' => 'Column'
        ];
        $validated = request()->validate([
            'description' =>  'required',
            'floor' =>  'required',
            'row' =>  'required|numeric|min:0|not_in:0',
            'column' =>  'required|numeric|min:0|not_in:0',
        ], [], $customFieldNames);

        $create = Maintenance::where('id', $id)->first();

        if ($create) {
            $create->fill($validated);
        } else {
            $validated["area_code"] = self::generateAreaCode(); //generate area code and replace
            $create = new Maintenance($validated);
        }
        
        $store = $create->save();
        
        if ($store) {
            $currentTable = MaintenanceItem::where('maintenance_id', $create->id)->get();
            if(count($currentTable) > 0){
                MaintenanceItem::where('maintenance_id', $create->id)->delete();
                $maintenance_items = self::createArrayItems($validated["row"],$validated["column"],$create->id,true,$currentTable);
            }
            else {
                $maintenance_items = self::createArrayItems($validated["row"],$validated["column"],$create->id,false,$currentTable);
            }

            MaintenanceItem::insert($maintenance_items);

            $row_data = Maintenance::find($create->id);
            $response['stat'] = "success";
            $response['message'] = 'Success';
            $response['toast_message'] = 'Awesome! Maintenance saved.';
            $response["row_data"] = $row_data;
        } else {
            $response['stat'] = "error";
            $response['title'] = 'Failed';
            $response['message'] = 'Ooops! Something went wrong.';
        }
        return $response;
    }

    public function delete()
    {
        $maintenance = Maintenance::where('id', request()->id)->first();
        $maintenance->delete();
        MaintenanceItem::where('maintenance_id', request()->id)->delete(); //also delete its items
        if ($maintenance) {
            $response['stat'] = "success";
            $response['title'] = 'Success';
            $response['message'] = 'Awesome! Maintenance Deleted.';
        } else {
            $response['stat'] = "error";
            $response['title'] = 'Failed';
            $response['message'] = 'Ooops! Something went wrong.';
        }
        return $response;
    }

    public function previewItems()
    {
        $maintenance = MaintenanceItem::with('tablestatus')->where('maintenance_id', request()->id)->get();
    	return $maintenance;
    }

    public function updateItem()
    {
        $customFieldNames = [
            'table_status_id' => 'Table Status'
        ];
        $validated = request()->validate([
            'table_status_id' =>  'required|numeric'
        ], [], $customFieldNames);
        
        $maintenance = MaintenanceItem::where('id', request()->id)->first();
        $maintenance->fill($validated);
        $maintenance->save();
        if ($maintenance) {
            $response['stat'] = "success";
            $response['title'] = 'Success';
            $response['message'] = 'Awesome! Maintenance Item Updated.';
        } else {
            $response['stat'] = "error";
            $response['title'] = 'Failed';
            $response['message'] = 'Ooops! Something went wrong.';
        }
        return $response;
    }

    public function generateAreaCode()
    {
        $area_code = mt_rand(100, 9999);
        while(Maintenance::where('area_code', $area_code)->count() > 0){
           $area_code = mt_rand(100, 9999);
        }
        return $area_code;
    }

    public function findLastValue($currentTable, $description, $maintenance_id)
    {
        foreach($currentTable->toArray() as $current){
            if(in_array($description, $current) && $current['maintenance_id'] == $maintenance_id) {
                return $current['table_status_id'];
            }
        }

        return 2;//return as active if not found
    }

    public function createArrayItems($row,$col,$id,$type,$currentTable){
        for ($r=1;$r<=$row;$r++) {
            for($c=1;$c<=$col;$c++){
                $description = 'R'.$r.'C'.$c;
                $table_status_id = ($type) ? self::findLastValue($currentTable, $description, $id) : '2';
                $maintenance_items[] = array(
                    'description'=> $description,
                    'maintenance_id'=> $id,
                    'table_status_id'=> $table_status_id,
                    'row_position'=> $r,
                    'col_position'=> $c,
                );
            }
        }

        return $maintenance_items;
    }
}
