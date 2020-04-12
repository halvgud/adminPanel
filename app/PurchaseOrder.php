<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    public $table = 'purchase_orders';
    public $fillable = ['orderedby', 'taxid', 'billto','customername','shiptoaddress'];
    use SoftDeletes;
    protected $dates = ['deleted_at'];

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
    public function getQtyBrowseAttribute()
    {
        return number_format($this->qty, 2);
    }
    public function getUnitPriceBrowseAttribute()
    {
        return '$' . number_format($this->unit_price, 2);
    }
    public function getPaymentTotalBrowseAttribute()
    {
        return '$' . number_format($this->payment_total, 2);
    }
    public function getPaymentAmountBrowseAttribute()
    {
        return '$' . number_format($this->payment_amount, 2);
    }
   
}