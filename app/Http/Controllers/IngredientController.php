<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ingredient;

class IngredientController extends Controller
{
    public function index(){
        return view('ingredient.index');
    }
    public function create(Request $req){
        $data = array();
        $headers = array();
        if($req->all()){
            try {
                //code...
                $product = new Ingredient();
                $product->fullname = $req->description;
                $product->unidad = $req->unidad;
                $product->save();

                $data["success"] = true;
                $data["data"] = $product;
                $data["message"] = "Registrado Correcto";
                return redirect()->route('ingredient.index');
            } catch (\Throwable $th) {
                //throw $th;
                $data["success"] = false;
                $data["message"] = $th->getMessage();
            }

            return response()->json($data, 200, $headers);
        }
    }

    public function update(Request $req,$id){
        $data = array();
        $headers = array();
        try {
            //code...
            $datos = Ingredient::where('id','=',$id)->first();

            if($req->all()){
                $datos->fullname = $req->description;
                $datos->unidad = $req->unidad;
                $datos->save();

                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Editado Correcto";
                return redirect()->route('ingredient.index');
            }else{
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Consultado Correcto";
            }

        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
        }
        return response()->json($data, 200, $headers);
    }

    public function delete($id){
        $data = array();
        try {
            //code...
            if(is_array($id)){

            }else{
                $datos = Ingredient::where('id','=',$id)->delete();
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Eliminado correcto";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
        }
    }

    public function getTable(){
        $data = array();
        $headers = array();

        $datos = Ingredient::all();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";

            $buttons.= "<a class='btn btn-warning m-1' href='javascript:edit(".$value->id.")'>Editar<a>";

            $buttons.= "<a class='btn btn-danger m-1' href='javascript:eliminar(".$value->id.")'>Eliminar<a>";

            $data['data'][$key] = [
                $key+1,
                $value->fullname,
                $value->unidad,
                $buttons
            ];
        }
        return response()->json($data, 200, $headers);
    }


    public function getData()
    {
        $termVal = $_GET["termVal"];
        $data = Ingredient::where('fullname','like','%'.$termVal.'%')->get();
        return response()->json($data, 200, []);
    }
}
