<?php

namespace App\Bll;

class ExportCSV
{
    private $file;
    private $data;
    private $columns;

    /**
     * ExportCSV constructor.
     *
     * @param $file
     * @param $data
     * @param $columns
     */
    public function __construct($file, $data, $columns)
    {
        $this->file = $file;
        $this->data = $data;
        $this->columns = $columns;
    }

    /**
     * Export CSV file
     *
     * @return string|null
     */
    public function exportCsv()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$this->file",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'wb');
            fputcsv($file, $this->columns);
            foreach ($this->data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
