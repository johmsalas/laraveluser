<?php

namespace App\Repositories;

use Excel;

class ExportRepository
{
    public function downloadXLS($name, $collection) {
        $this->excelExport($collection, $name, 'xls');
    }

    public function downloadXLSX($name, $collection) {
        $this->excelExport($collection, $name);
    }

    public function downloadTSV($name, $collection) {
        $this->SVExport('tsv', $collection, function (&$vals, $key, $output) {
            fputcsv($output, $vals, "\t", '"');
        }, $name);
    }

    public function downloadCSV($name, $collection) {
        $this->SVExport('csv', $collection, function (&$vals, $key, $output) {
            fputcsv($output, $vals, ';', '"');
        }, $name);
    }

    private function SVExport($format, $collection, $formatter, $name = 'file') {
        header('Content-Type: text/' . $format . '; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $name . '.' . $format);

        $data = $collection->toArray();
        $outstream = fopen("php://output", 'w');
        array_walk($data, $formatter, $outstream);
        fclose($outstream);
    }

    private function excelExport($collection, $name = 'filename', $ext = 'xlsx') {
        Excel::create($name, function($excel) use ($collection) {
            $excel->setTitle(trans('Users'));
            $excel->sheet(trans('Users'), function($sheet) use ($collection) {
                $sheet->fromArray($collection->toArray());
            });
        })->export($ext);
    }
}
