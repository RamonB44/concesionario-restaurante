<?php

namespace App\Excel;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Style\Border;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Orders implements FromArray, WithHeadings,WithEvents,ShouldAutoSize
{
    //
    protected $invoices;
    protected $headings;
    protected $painting;
    protected static $static_paint;

    use Exportable, RegistersEventListeners;
        //

        public function __construct(array $invoices,array $headings,$painting)
        {
            $this->invoices = $invoices;
            $this->headings = $headings;
            $this->painting = $painting;
            self::$static_paint = $painting;
        }

        public function array(): array
        {
            return $this->invoices;
        }

        public function headings(): array
        {
            return $this->headings;
        }

        public static function afterSheet(AfterSheet $event)
        {
                $cell = self::$static_paint;
                $event->sheet->getDelegate()->getStyle($cell.'1:'.$cell.$event->sheet->getDelegate()->getHighestRow())
                ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffff15');
        }

        // public function registerEvents(): array
        // {
        //     return [
        //         AfterSheet::class    => function(AfterSheet $event) {

        //             $event->sheet->styleCells(
        //                 $this->painting.'1:'.$this->painting.$event->sheet->getDelegate()->getHighestRow(),
        //                 [
        //                     'borders' => [
        //                         'outline' => [
        //                             // 'setFillType' => Fill::FILL_SOLID,
        //                             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
        //                             'color' => ['argb' => 'ffff15'],
        //                         ],
        //                     ]
        //                 ]
        //             );
        //         },
        //     ];
        // }
}
