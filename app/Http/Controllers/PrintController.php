<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;

class PrintController extends Controller
{
    //
    private static $current;
    public static function getInstance()
    {
        if (!self::$current instanceof self) {
            self::$current = new self();
        }

        return self::$current;
    }

    public function directPrint($req){
        // return $req['data']['product'];exit;
        $profile = CapabilityProfile::load("simple");
        //$connector = new CupsPrintConnector("EPSON TM-T20II");
        $connector = new WindowsPrintConnector("EPSON");
        // //$connector = new NetworkPrintConnector("192.168.1.15");

        // $connector = new CupsPrintConnector($printerName);
        $impresora = new Printer($connector);
        $impresora->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $impresora->setEmphasis(true);
        $impresora->setFont(Printer::FONT_A);
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setTextSize(3, 3);
        $impresora->text($req['order']['employe']['code']." N° ".$req['order']['id']."\n");
        $impresora->feed();
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $count = count($req['data']['product']);
        $impresora->setTextSize(2, 2);
        $product = array();
        $true = true;
        $contador = 0;
        $x = true;
        // $contador2 = 0;
        for ($i=0; $i < $count; $i++) {
            # code...

            if($x){
                if($req['data']['remited'][$i]['value']==0 && $req['data']['qty_plus'][$i]['value']==0){
                    $product[$contador]["id"] = $req['data']['product'][$i]['value'];
                    $product[$contador]["nombre"] = $req['data']['product_name'][$i]['value'];
                    $product[$contador]["qty"] = $req['data']['qty'][$i]['value'];
                    $x = false;
                }elseif($req['data']['remited'][$i]['value']==1 && $req['data']['qty_plus'][$i]['value']>0){
                    $product[$contador]["id"] = $req['data']['product'][$i]['value'];
                    $product[$contador]["nombre"] = $req['data']['product_name'][$i]['value'];
                    $product[$contador]["qty"] = $req['data']['qty_plus'][$i]['value'];
                    $x=false;
                }
            }else{
                for ($p=0; $p < count($product); $p++) {
                    # code...
                    // 1 - 2 - 4 - 2 = 2
                    if($product[$p]["id"] == $req['data']['product'][$i]['value']){
                        $true = true;
                        break;
                    }else{
                        $true = false;
                    }

                }

                if($true){
                    if($req['data']['remited'][$i]['value']==0 && $req['data']['qty_plus'][$i]['value']==0){
                        $product[$contador]["qty"] = $product[$contador]["qty"] + $req['data']['qty'][$i]['value'];
                    }elseif($req['data']['remited'][$i]['value']==1 && $req['data']['qty_plus'][$i]['value']>0){
                        $product[$contador]["qty"] = $product[$contador]["qty"] + $req['data']['qty_plus'][$i]['value'];
                    }
                    // $true = true;
                }else{
                    // $product[$x]["qty"] = $product[$x]["qty"] + $req['data']['qty'][$i]['value'];
                    if($req['data']['remited'][$i]['value']==0 && $req['data']['qty_plus'][$i]['value']==0){
                        $contador++;
                        $product[$contador]["id"] = $req['data']['product'][$i]['value'];
                        $product[$contador]["nombre"] = $req['data']['product_name'][$i]['value'];
                        $product[$contador]["qty"] = $req['data']['qty'][$i]['value'];
                        $x=false;
                    }elseif($req['data']['remited'][$i]['value']==1 && $req['data']['qty_plus'][$i]['value']>0){
                        $contador++;
                        $product[$contador]["id"] = $req['data']['product'][$i]['value'];
                        $product[$contador]["nombre"] = $req['data']['product_name'][$i]['value'];
                        $product[$contador]["qty"] = $req['data']['qty_plus'][$i]['value'];
                        $x=false;
                    }

                }

            }
        }
        foreach ($product as $key => $value) {
            $impresora->text($value['nombre']." | ".$value['qty']);
            $impresora->feed();
        }
        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setTextSize(1, 1);
        $impresora->feed();
        $impresora->text("Emitido por CONCECIONARIA\n");
        $impresora->text("Gracias por su preferencia");
        $impresora->feed(2);
        $impresora->cut();
        $impresora->close();
    }

    public function printBarcode($codigos){
        if(is_array($codigos)){
            $connector = new WindowsPrintConnector("EPSON-GENERICO");
            $impresora = new Printer($connector);
            foreach ($codigos as $key => $value) {
                # code...
                $impresora->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
                $impresora->setEmphasis(true);
                $impresora->setFont(Printer::FONT_A);
                $impresora->setJustification(Printer::JUSTIFY_CENTER);
                $impresora->setTextSize(3, 3);
                $impresora->setBarcodeHeight(150);
                $impresora->setBarcodeWidth(255);
                $impresora->barcode($value, Printer::BARCODE_CODE39);
                $impresora->setTextSize(1, 1);
                $impresora->text($value);
                $impresora->feed(2);
                $impresora->cut();
            }
            $impresora->close();
        }
    }
}
