<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Maintenance;

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
            'row' =>  'required',
            'column' =>  'required',
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
}
