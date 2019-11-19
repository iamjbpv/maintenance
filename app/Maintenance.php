<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    protected $table = 'maintenance';
    use SoftDeletes;
    protected $guarded = ['created_at', 'updated_at', 'deleted_at'];


}
