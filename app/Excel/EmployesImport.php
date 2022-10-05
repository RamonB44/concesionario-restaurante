<?php

namespace App\Excel;

// use Illuminate\Database\Eloquent\Model;
use App\Employes;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EmployesImport implements ToModel,WithChunkReading,ShouldAutoSize
{
    use ShouldAutoSize;

    public function model(array $row)
    {
        return new Employes([
            'code' => $row[0],
            'valid' => $row[1],
            'doc_num' => $row[2],
            'fullname' => $row[3],
            'area' => $row[4],
        ]);
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
