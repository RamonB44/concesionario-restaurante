<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    //
    use SoftDeletes;

    protected $table = "orden";

    public function employe()
    {
        return $this->belongsTo('App\Employes', 'id_employe');
    }

    public function detail()
    {
        return $this->hasMany('App\OrderDetail', 'id_order', 'id');
    }
}
