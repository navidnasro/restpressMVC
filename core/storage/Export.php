<?php

namespace sellerhub\core\storage;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use sellerhub\bootstraps\Environment;

class Export
{
    public static function toExcel(array $headers, array $data, string $fileName): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add header row to the sheet
        $sheet->fromArray($headers, NULL, 'A1');

        // Add user data rows
        $row = 2; // Start from the second row for data
        foreach ($data as $item)
        {
            $sheet->fromArray($item, NULL, 'A' . $row);
            $row++;
        }

        // Save the file
        $writer = new Xlsx($spreadsheet);

        // Get WordPress uploads directory
        $uploadsDir = wp_upload_dir();
        $uploadPath = $uploadsDir['basedir'] . '/temp/';

        // Ensure the temp directory exists and Create directory if it doesn't exist
        if (!file_exists($uploadPath))
            wp_mkdir_p($uploadPath);

        $tempPath = $uploadPath.$fileName.'.xlsx';

//        $tempPath = Environment::Storage.'/app/temp/'.$fileName.'.xlsx';
//
//        if (!is_dir(Environment::Storage.'/app'))
//            mkdir(Environment::Storage.'/app', 0755, true);
//
//        if (!is_dir(Environment::Storage.'/app/temp'))
//            mkdir(Environment::Storage.'/app/temp', 0755, true);

        $writer->save($tempPath);

        return $uploadsDir['baseurl'].'/temp/'.$fileName.'.xlsx';
    }

    public static function htmlToPdf(string $html, string $fileName): string
    {
        $mpdf = new Mpdf(
            [
                'fontDir' => [Environment::PublicFolder.'/assets/fonts'],
                'fontdata' => [
                    'yekanbakh' => [
                        'R' => 'YekanBakhFaNum-Regular.ttf',
                        'B' => 'YekanBakhFaNum-Bold.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ],
                ],
                'directionality' => 'rtl'
            ]
        );

        $mpdf->SetFont('yekanbakh', '', 12);
        $mpdf->WriteHTML($html);

        // Get WordPress uploads directory
        $uploadsDir = wp_upload_dir();
        $uploadPath = $uploadsDir['basedir'] . '/temp/';

        // Ensure the temp directory exists and Create directory if it doesn't exist
        if (!file_exists($uploadPath))
            wp_mkdir_p($uploadPath);

        $tempPath = $uploadPath.$fileName.'.pdf';

//        $tempPath = Environment::Storage.'/app/temp/'.$fileName.'.pdf';

//        if (!is_dir(Environment::Storage.'/app'))
//            mkdir(Environment::Storage.'/app', 0755, true);
//
//        if (!is_dir(Environment::Storage.'/app/temp'))
//            mkdir(Environment::Storage.'/app/temp', 0755, true);

        $mpdf->Output($tempPath, Destination::FILE);

        return $uploadsDir['baseurl'].'/temp/'.$fileName.'.pdf';
    }
}