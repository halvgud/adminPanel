<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class PurchaseOrder extends Model
{
    public $table = 'purchase_orders';
    public $fillable = ['orderedby', 'taxid', 'billto','customername','shiptoaddress'];

    public function productId()
    {
        return $this->belongsTo('App\Product');
    }
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->warehouse_id = \Auth::user()->role_id;
        });
    }
    
    public function scopeCurrentRoleId($query)
    {
        $currWar = \Auth::user()->role_id;
        if($currWar!='1'&&$currWar!='3'){
            return $query->where('warehouse_id', \Auth::user()->role_id);
        }else {
            return $query;
        }
    }
}