<?php

namespace App\Http\Controllers;

use App\Excel\Orders;
use App\Employes;
use App\Order;
use App\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    //

    public function getXlsx2(){
        $items = array();
        $today = Carbon::today();
        $orden = Order::where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '=', $today->format('Y-m-d'))->get();
        //$ordendetail = OrderDetail::where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '=', $today->format('Y-m-d'))->get();
        foreach ($orden as $key => $value) {
            # code...
            $items[$key][] = "Orden N°".$value->id;
            $items[$key][] = $value->employe->code;
            $items[$key][] = $value->employe->fullname;
            $items[$key][] = $value->net_amount_value;
            $items[$key][] = $value->created_at;
        }
        $export = new Orders($items);
        return  Excel::download($export, 'xlsx_'.$today->format('Y-m-d').'.xlsx');
    }

    public function getXlsx(){
        $items = array();

        $today = Carbon::today();
        $now = Carbon::now();

        $weekStartDate = $now->startOfWeek()->format('Y-m-d H:i:s');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d H:i:s');
        // return $weekStartDate . "|" . $weekEndDate;
        $fecha1 = Carbon::parse($weekStartDate);
        $fecha2 = Carbon::parse($weekEndDate);
        // return $fecha1->addDays(1) . "|" . $fecha2;
        $diff = $fecha1->diff($fecha2);

        $paint_row = "";
        //row we would be paint in excel
        $painting = array('E','F','G','H','I','J','K');

        $headings = array();

        $netobyEmploye = 0;

        $employes = Employes::all();

        foreach ($employes as $key => $value) {
            # code...
            $items[$key]["Employe_ID"] = $value->id;
            $items[$key]["Codigo"] = $value->code;
            $items[$key]["Documento"] = $value->doc_num;
            $items[$key]["Nombres"] = $value->fullname;
        }

        foreach ($items as $k => $v) {
            # code...

            for ($i=0; $i < $diff->days +1; $i++) {
                # code...
                $fecha1 = Carbon::parse($weekStartDate);
                $current = $fecha1->addDays($i);
                $orden = Order::select(DB::raw('case when sum(net_amount_value) != 0 then sum(net_amount_value) else 0 end as total'))->where('id_employe','=',$items[$k]["Employe_ID"])->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '=', $current->format('Y-m-d'))->first();
                $items[$k][$current->format('Y-m-d')]  = $orden->total;
                $netobyEmploye = $netobyEmploye + $orden->total;
            }
            $items[$k][]  = $netobyEmploye;
            $netobyEmploye = 0;

        }
        // return $items;

        $headings[0] = "ID";
        $headings[1] = "Codigo";
        $headings[2] = "Documento";
        $headings[3] = "Nombres";

        for ($i=0; $i < $diff->days +1; $i++) {
            # code...
            $fecha1 = Carbon::parse($weekStartDate);
            $current = $fecha1->addDays($i);
            if($current->format('Y-m-d') == $today->format('Y-m-d')){
                $paint_row = $painting[$i];
            }
            $headings[count($headings)] = $current->format('Y-m-d') ;
        }
        $headings[] = "TOTAL";
        // return $headings;

        $export = new Orders($items,$headings,$paint_row);
        return  Excel::download($export, 'xlsx_'.$today->format('Y-m-d').'.xlsx');
    }
}
