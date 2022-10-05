<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    //
    use SoftDeletes;

    protected $table = 'ordendetail';
    protected $fillable = ['id_order','id_product','qty','rate_price','is_credit'];

    public function products()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }
}
