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
}