<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stock;

class StockController extends Controller
{
    //

    public function index(){
        return view('stock.index');
    }

    public function create(Request $req){
        if($req->all()){

        }else{
            return view('stock.create');
        }
    }

    public function edit(Request $req,$id){
        if($req->all()){

        }else{
            return view('stock.edit');
        }
    }

    public function delete($id){
        $data = array();
        $headers = array();

        if(is_array($id)){

        }else{

        }

        return response()->json($data, 200, $headers);
    }

    public function getTable(){
        $data = array();
        $headers = array();
        $datos = Stock::where('id','=',1)->first();
        foreach ($datos->ingredient_stock as $key => $value) {
            # code...
            $buttons = "";

            $data['data'][$key] = [
                $key+1,
                $value->fullname,
                $value->pivot->qty,
                $buttons
            ];
        }

        return response()->json($data, 200, $headers);
    }

    public function regulate(Request $req){
        if($req->all()){
            return redirect()->route('stock.index');
        }else{
            return view('stock.regulate');
        }
    }
}
