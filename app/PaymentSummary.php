<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class PaymentSummary extends Model
{
    public $table = 'payment_summary_view';
    protected $primaryKey = 'rowId';

    public function getPaymentTotalBrowseAttribute()
    {
        return '$' . number_format($this->payment_total, 2);
    }
    public function getPaymentAmountBrowseAttribute()
    {
        return '$' . number_format($this->payment_amount, 2);
    }
    public function getAmountDueBrowseAttribute()
    {
        return '$' . number_format($this->amount_due, 2);
    }
}
