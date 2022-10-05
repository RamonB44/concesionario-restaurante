<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employes;
use App\Excel\EmployesImport;
use App\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EmployesController extends Controller
{
    //
    public function index(){
        return view('employes.index');
    }
    public function create(Request $req){
        $data = array();
        $headers = array();
        if($req->all()){

            try {
                //code...
                $employe = new Employes();
                $employe->code = $req->code;
                $employe->doc_num = $req->docnum;
                $employe->fullname = $req->fullname;
                $employe->id_area = $req->area;
                $employe->save();

                $data["success"] = true;
                $data["data"] = $employe;
                $data["message"] = "Registrado Correcto";

            } catch (\Throwable $th) {
                //throw $th;
                $data["success"] = false;
                $data["message"] = $th->getMessage();
            }
            return redirect()->route('employes.index');
        }

    }

    public function update(Request $req,$id){
        $data = array();
        $headers = array();

        try {
            //code...
            $datos = Employes::where('id','=',$id)->first();

            if($req->all()){
                $datos->code = $req->code;
                $datos->doc_num = $req->docnum;
                $datos->fullname = $req->fullname;
                $datos->id_area = $req->area;
                $datos->save();

                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Editado Correcto";
                return redirect()->route('employes.index');
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
        $headers = array();
        try {
            //code...
            if(is_array($id)){

            }else{
                $datos = Employes::where('id','=',$id)->delete();
                $data["success"] = true;
                $data["data"] = $datos;
                $data["message"] = "Eliminado correcto";
            }
        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
        }
        return response()->json($data, 200, $headers);
    }

    public function generateNewCode($id){
        $data = array();
        $headers = array();
        try {
            //code...
            $datos = Employes::where('id','=',$id)->first();
            if($datos->valid == 9){
                $datos->valid = 1;
            }else{
                $datos->valid = $datos->valid + 1;
            }
            $data["success"] = true;
            $data["data"] = $datos;
            $data["message"] = "Actualizado codigo de validacion";
            $datos->save();
        } catch (\Throwable $th) {
            //throw $th;
            $data["success"] = false;
            $data["message"] = $th->getMessage();
        }
        return response()->json($data, 200, $headers);

    }

    public function getTable(){
        $data = array();
        $headers = array();

        $datos = Employes::all();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";
            $buttons.= "<a class='btn btn-dark m-1' href='javascript:generateNew(".$value->id.")'>Generar<a>";
            $buttons.= "<a class='btn btn-warning m-1' href='javascript:edit(".$value->id.")'>Editar<a>";

            $buttons.= "<a class='btn btn-danger m-1' href='javascript:eliminar(".$value->id.")'>Eliminar<a>";

            $data['data'][$key] = [
                $key+1,
                $value->code,
                $value->valid,
                $value->doc_num,
                $value->fullname,
                $value->areas->area,
                $buttons
            ];
        }
        return response()->json($data, 200, $headers);
    }

    public function getEmploye($string){
        $data = array();
        $headers = array();
        $today = Carbon::today();
        $count = strlen($string);
        // return substr($string,-1);
        if(strlen($string)>0){

            $datos = Employes::where(DB::raw('concat(code , valid)'),'=',$string)->orWhere('doc_num','=',$string)->first();

            if($datos){
                $order = Order::where('id_employe','=',$datos->id)->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '=', $today->format('Y-m-d'))->first();
                if(isset($order->detail)){
                    $order->detail;
                    foreach ($order->detail as $key => $value) {
                        # code...
                        $value->products;
                    }
                }
                $data["order"] = $order;
                $data["success"] = true;
                $data["data"] = $datos;
            }else{
                $data["success"] = false;
            }
        }


        return response()->json($data, 200, $headers);
    }

    public function printBarcode(Request $req){
        $print = PrintController::getInstance();

        $print->printBarcode($req->ids);
    }

    public function importEmploye(Request $req){
        // $req->hasFile('file');
        // return $req->all();
        if($req->all()){
            // return $req->all();
            // $this->validate($req, [
            //     'file_xlsx.*' => 'mimes:xlsx'
            // ]);

            if($req->hasfile('file_xlsx')){
                // echo "hola";
                // $filename = $req->file('file_xlsx');
                // return $filename;

                return Excel::import(new EmployesImport,$req->file('file_xlsx'));
            }
        }
        // return $req->all();
        // exit;

    }
}
