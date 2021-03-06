<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class InboundReceiving extends Model
{
    public $table= 'inbound_receivings';
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->warehouse_id = \Auth::user()->role_id;
            $model->user_id = \Auth::user()->id;
        });
    }

    public function scopeCurrentRoleId($query)
    {
        $currWar = \Auth::user()->role_id;
        if ($currWar != '1' && $currWar != '3') {
            return $query->where('warehouse_id', \Auth::user()->role_id);
        } else {
            return $query;
        }
    }
}
