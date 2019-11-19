<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceItem extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public function tablestatus()
    {
        return $this->hasOne('App\TableStatus', 'id', 'table_status_id');
    }
}
