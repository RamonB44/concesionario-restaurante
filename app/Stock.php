<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    //
    protected $table = "stock";

    public function ingredient_stock(){
        return $this->belongsToMany('App\Ingredient','ingredient_stock','id_stock','id_ingredient')
        ->withPivot(['qty'])->withTimestamps();
    }
}
