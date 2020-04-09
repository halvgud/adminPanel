<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth;


class Product extends Model
{
    public $table = 'products';
    protected $primaryKey = 'id';

    public $fillable = ['name','upc','product_reference','product','sku','palletsize','cartonsize'];
    /**
     * The shops that belong to the product.
     */
    public function vendorId()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function vendorLocation(){
        return $this->belongsTo('App\VendorLocation','location');
    }

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
