<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Warehouse extends Model
{
    protected $primaryKey = 'id';

    public $table = 'warehouses';
    public $fillable = ['name'];
    
}