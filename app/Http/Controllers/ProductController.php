<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;

class ProductController extends Controller
{
    //
    public function index(){
        $category = Category::all();
        return view('products.index',compact('category'));
    }

    public function create(Request $req){
        $data = array();
        $headers = array();
        if($req->all()){
            try {
                //code...
                $product = new Product();
                $product->fullname = $req->description;
                $product->rate_price = $req->rate_price;
                $product->id_category = $req->category;
                $product->contado_price = $req->contado_price;
                $product->save();

                $data["success"] = true;
                $data["data"] = $product;
                $data["message"] = "Registrado Correcto";

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
            $datos = Product::where('id','=',$id)->first();

            if($req->all()){
                $datos->fullname = $req->description;
                $datos->rate_price = $req->rate_price;
                $datos->contado_price = $req->contado_price;
                $datos->id_category = $req->category;
                $datos->save();

                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Editado Correcto";
                return redirect()->route('products.index');
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
                $datos = Product::where('id','=',$id)->delete();
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

        $datos = Product::all();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";

            $buttons.= "<a class='btn btn-warning m-1' href='javascript:edit(".$value->id.")'>Editar<a>";

            $buttons.= "<a class='btn btn-danger m-1' href='javascript:eliminar(".$value->id.")'>Eliminar<a>";

            $data['data'][$key] = [
                $key+1,
                $value->fullname,
                $value->rate_price,
                $value->contado_price,
                $buttons
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function getbyId($id){
        $data = array();
        $headers = array();
        $datos = Product::where('id','=',$id)->first();
        if($datos){
            $data['success'] = true;
            $data['data'] = $datos;
        }else{
            $data['success'] = false;
        }
        return response()->json($data, 200, $headers);
    }

}
