<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class OutboundProductSummary extends Model
{
    public $table = 'outbound_product_summary_view';
    protected $primaryKey = 'rowId'; 
}
