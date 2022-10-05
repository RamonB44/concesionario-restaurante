<?php

namespace App\Http\Controllers;

use App\Category;
use App\Employes;
use Illuminate\Http\Request;
use App\Order;
use Carbon\Carbon;
use App\OrderDetail;
use App\Product;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;

class OrderController extends Controller
{
    //

    public function index(){
        return view('order.index');
    }


    public function submitOrden(Request $req){
        $data = array();
        $headers = array();
        $category = Category::all();
        $product = Product::all();
        $today = Carbon::today();
        if($req->all()){
            try {
                //code...
                $print = PrintController::getInstance();
                $order = Order::where('id_employe','=',$req->employe)->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '=', $today->format('Y-m-d'))->first();
                if($order){
                    $order->net_amount_value = $req->net_amount_value;
                    // $order->id_employe = $req->employe;
                    $order->save();
                    $order->employe;
                    $count = count($req->product);
                    OrderDetail::where('id_order','=',$order->id)->delete();
                    for ($i=0; $i < $count; $i++) {
                        # code...
                        $items = [
                            'id_order' => $order->id,
                            'id_product' => $req->product[$i]['value'],
                            'qty' => $req->qty[$i]['value'],
                            'rate_price' => $req->rate_price[$i]['value'],
                            'is_credit' => $req->is_credit[$i]['value']
                        ];

                        $orderdetailt = new OrderDetail($items);
                        $orderdetailt->save();
                    }

                }else{
                    $order = new Order();
                    $order->net_amount_value = $req->net_amount_value;
                    $order->id_employe = $req->employe;
                    $order->save();
                    $order->employe;
                    $count = count($req->product);

                    for ($i=0; $i < $count; $i++) {
                        # code...
                        $items = [
                            'id_order' => $order->id,
                            'id_product' => $req->product[$i]['value'],
                            'qty' => $req->qty[$i]['value'],
                            'rate_price' => $req->rate_price[$i]['value'],
                            'is_credit' => $req->is_credit[$i]['value']

                        ];

                        $orderdetailt = new OrderDetail($items);
                        $orderdetailt->save();
                    }
                }


                $datos = array();
                $datos['data'] = $req->all();
                $datos['order'] = $order;
                $datos['success'] = true;

                $print->directPrint($datos);

            } catch (\Throwable $th) {
                //throw $th;
                $datos['success'] = true;
                $datos['message'] = $th->getMessage();
                return response()->json($datos, 200, []);
            }

            return response()->json($datos, 200, []);
        }else{
            return view('order.create',compact('category','product'));
        }

        return response()->json($data, 200, $headers);
    }

    public function deleteOrden($id){

        $data = array();
        $headers = array();
        try {
            //code...
            $datos = Order::where('id','=',$id)->delete();
            $detail = OrderDetail::where('id_order','=',$id)->delete();
            $data['order'] = $datos;
            $data['detail'] = $detail;
            $data['success'] = true;
            $data['message'] = "Eliminado Correctamente";
        } catch (\Throwable $th) {
            //throw $th;
            $data['success'] = false;
            $data['message'] = $th->getMessage();
        }

        return response()->json($data, 200, $headers);
    }

    public function getTable(){
        $data = array();
        $headers = array();
        $datos = Order::whereDate('created_at', Carbon::today())->get();

        foreach ($datos as $key => $value) {
            # code...
            $buttons = "";

            // $buttons.= "<a class='btn btn-warning m-1' href='javascript:edit(".$value->id.")'>Editar<a>";

            $buttons.= "<a class='btn btn-danger m-1' href='javascript:eliminar(".$value->id.")'>Eliminar<a>";

            $data['data'][$key] = [
                $key+1,
                "Orden N° ".$value->id,
                $value->net_amount_value,
                $buttons
            ];
        }

        return response()->json($data, 200, $headers);
    }

    public function searchByCodigo($codeOrdni,$start_date,$end_date){
        $start = Carbon::parse($start_date)->startOfDay();
        $end = Carbon::parse($end_date)->endOfDay();

            // return "STRTOTIME =>".strtotime($end->format('Y-m-d H:i'))."| NORMALDATE =>". $end->format('Y-m-d H:i');
        $data = Order::whereHas('employe',function($query) use ($codeOrdni){
            // if(strlen($codeOrdni)==8){
                $query->where('doc_num','=',$codeOrdni)->orWhere(DB::raw('concat(code , valid)'),'=',$codeOrdni);
            // }
        })->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(orden.created_at, "%Y-%m-%d"))'), [strtotime($start->format('Y-m-d H:i')), strtotime($end->format('Y-m-d H:i'))])->get();

        $employe = Employes::where('code','=',$codeOrdni)->orWhere('doc_num','=',$codeOrdni)->first();
        // return response()->json($data, 200, []);
        return view('consultas.response',compact('data','employe'))->render();
    }
}
