<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    //

    public function index(){
        return view('reports.index');
    }

    public function getPDF($variable,$start,$end){

        switch ($variable) {
            case 1:
                # code...
                break;
            case 2:
                # code...
                break;
            case 3:
                # code...
                break;
            case 4:
                # code...
                break;
            case 5:
                # code...
                break;
            default:
                # code...
                break;
        }
    }

    public function showPDF(){

    }
}
