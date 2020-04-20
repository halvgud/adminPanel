<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;


class Forecast extends Model
{
    public $table = 'forecast';
    protected $primaryKey = 'id';
    use SoftDeletes;
    protected $dates = ['deleted_at'];


    /**
     * The shops that belong to the product.
     */


   /* public static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->warehouse_id = \Auth::user()->warehouse_id;
        });
    }*/
    /*public function scopeCurrentWarehouse($query)
    {
        return $query->where('warehouse_id', \Auth::user()->warehouse_id);
    }*/
}
