<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceItem extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
}
