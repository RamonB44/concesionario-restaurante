<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employes extends Model
{
    //
    protected $table = "employes";
    public function areas()
    {
        return $this->belongsTo('App\Area', 'area');
    }

}
