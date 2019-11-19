<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Maintenance;
use App\MaintenanceItem;

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
        $title = 'Maintenance';

    	return view('maintenance', compact('maintenance', 'title'));
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
            // echo count($currentTable);
            // print_r($currentTable);
            if(count($currentTable) > 0){
                $deletedRows = MaintenanceItem::where('maintenance_id', $create->id)->delete();
                for ($r=1;$r<=$validated["row"];$r++) {
                    for($c=1;$c<=$validated["column"];$c++){
                        $description = 'R'.$r.'C'.$c;
                        $table_status_id = self::findLastValue($currentTable, $description, $create->id);
                        $maintenance_items[] = array('description'=> $description, 'maintenance_id'=> $create->id, 'table_status_id'=> $table_status_id );
                    }
                }
                MaintenanceItem::insert($maintenance_items);
            }
            else {
                for ($r=1;$r<=$validated["row"];$r++) {
                    for($c=1;$c<=$validated["column"];$c++){
                        $description = 'R'.$r.'C'.$c;
                        $maintenance_items[] = array('description'=> $description, 'maintenance_id'=> $create->id, 'table_status_id'=> '2' );
                    }
                }
                MaintenanceItem::insert($maintenance_items);
            }

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

    public function generateAreaCode()
    {
        return mt_rand(100, 999);
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
}
