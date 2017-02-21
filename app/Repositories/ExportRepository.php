<?php

namespace App\Repositories;

use Excel;

class ExportRepository
{
    /**
     * Prints a Laravel collection using XLS format to the output buffer
     * @param  string $name of the file
     * @param  Collection $collection
     */
    public function downloadXLS($name, $collection) {
        $this->excelExport($collection, $name, 'xls');
    }

    /**
     * Prints a Laravel collection using XLSX format to the output buffer
     * @param  string $name of the file
     * @param  Collection $collection
     */
    public function downloadXLSX($name, $collection) {
        $this->excelExport($collection, $name);
    }

    /**
     * Prints a Laravel collection using TSV format to the output buffer
     * @param  string $name of the file
     * @param  Collection $collection
     */
    public function downloadTSV($name, $collection) {
        // Another way to do this is using laravel-excel which support tsv
        $this->SVExport('tsv', $collection, function (&$vals, $key, $output) {
            fputcsv($output, $vals, "\t", '"', "\n");
        }, $name);
    }

    /**
     * Prints a Laravel collection using CSV format to the output buffer
     * @param  string $name of the file
     * @param  Collection $collection
     */
    public function downloadCSV($name, $collection) {
        // Another way to do this is using laravel-excel which support csv
        $this->SVExport('csv', $collection, function (&$vals, $key, $output) {
            fputcsv($output, $vals, ',', '"', "\n");
        }, $name);
    }

    /**
     * Prints a Laravel collection using *SV format to the output buffer
     * @param string $format
     * @param Collection $collection
     * @param function $formatter
     * @param string $name of the file
     */
    private function SVExport($format, $collection, $formatter, $name = 'file') {
        header('Content-Type: text/' . $format . '; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $name . '.' . $format);

        $data = $collection->toArray();
        $outstream = fopen("php://output", 'w');
        array_walk($data, $formatter, $outstream);
        fclose($outstream);
    }

    /**
     * Prints a Laravel collection an excel format to the output buffer
     * @param  Collection $collection
     * @param  string $name
     * @param  string $ext
     */
    private function excelExport($collection, $name = 'filename', $ext = 'xlsx') {
        Excel::create($name, function($excel) use ($collection) {
            $excel->setTitle(trans('Users'));
            $excel->sheet(trans('Users'), function($sheet) use ($collection) {
                $sheet->fromArray($collection->toArray());
            });
        })->export($ext);
    }
}
