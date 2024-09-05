<?php
require("includes/init.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$defautlError = false;

// Paremetre date
$date_now = new DateTime();

$periode = $_GET['periode'];
$destination = $_GET['dest'] == 'null' ? null : $_GET['dest'];
$type = $_GET['type'] == 'null' ? null : $_GET['type'];
$residence = $_GET['residence'] == 'null' ? null : $_GET['residence'];
$statut = $_GET['statut'] == 'null' ? null : $_GET['statut'];

$param_stats = $storedProcedure->call('liste_param_stats', [
    ['value'=>$util_id, 'type'=>'INT'],
], true, PDO::FETCH_ASSOC, 1);

$invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']');
$file_name = str_replace($invalidCharacters,'_', 'Export Offre comm');
$file_subname = 'Tableau Statistique';
$keySearch = array_search($periode, array_column($param_stats, 'k'));
if($keySearch !== false){
    $periodeValue = $param_stats[$keySearch]['v'];
    $file_name .= ' - ' . $periode;
    $file_subname = substr($periodeValue, 0, 30);;
}
$file_name = str_replace($invalidCharacters,'', $file_name);
$file_subname = str_replace($invalidCharacters,'', $file_subname);

$params = [];
$params[] = ['value' => $util_id, 'type' => 'TEXT'];
$params[] = ['value' => $periode, 'type' => 'TEXT'];
$params[] = ['value' => $destination, 'type' => 'TEXT'];
$params[] = ['value' => $type, 'type' => 'TEXT'];
$params[] = ['value' => $residence, 'type' => 'TEXT'];
$params[] = ['value' => $statut, 'type' => 'TEXT'];


$liste_to_export = $storedProcedure->call('liste_export_statistique', $params);
//$defautlError = true;
if($defautlError){
//    echo "<h4>Erreur de paramètre d'export</h4>";
    echo "<h4>L'export est momentanement indisponible.</h4>";
    die();
}

require_once("vendor/autoload.php");



/**
 *  ---------- Initialisation des données ----------
 */


$columns = range('A', 'Z');
$result_column = array_keys($liste_to_export[0]);



if(count($result_column) > 26){
    $col = 0;
    while(count($result_column) > count($columns)){
        for($i = 0; $i < 26; $i++){
            $columns[] = $columns[$col] . $columns[$i];
        }
        $col++;
    }
}


$spreadsheet = new Spreadsheet();

// Setting font to Calibri (Corps)
$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri (Corps)');

// Setting font size to 12
$spreadsheet->getDefaultStyle()->getFont()->setSize(11);


//Setting description, creator and title
$spreadsheet ->getProperties()->setTitle($file_name);
$spreadsheet ->getProperties()->setCreator("Export Offre comm");
$spreadsheet ->getProperties()->setDescription($file_name);
$writer = new Xlsx($spreadsheet);
$sheet = $spreadsheet ->getActiveSheet();


// Setting title of the sheet
$sheet->setTitle($file_subname);

$sheet->getRowDimension('1')->setRowHeight(20);

$colNb = 0;
foreach ($result_column as $columnTitle){
    $cell = $columns[$colNb] . '1';

    if(strpos($columnTitle, "*") !== false){
        $explode = explode('*', $columnTitle);
        $columnTitle = $explode[1];
    }

    $sheet->getCell($cell)->setValue($columnTitle);
    $sheet->getStyle($cell)->applyFromArray(
        array(
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => array('rgb' => '1EA1E1')
            ),
            'borders'=> array(
                'outline'=> array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            ),
            'font' => array(
                'color' => array('rgb' => 'E9F5FC'),
                'bold'  => true,
            )
        )
    );
    $colNb++;
}




// Set Values
$i = 2;
$colNb = 0;
foreach ($liste_to_export as $values){
    $colNb = 0;
    foreach ($values as $key => $value){
        $nextCol = true;
        $cell = $columns[$colNb] . $i;


        if(strpos($key, "*") !== false){
            $explode = explode('*', $key);
            $type = $explode[0];
            if($type == "TEXT"){
                $sheet->setCellValueExplicit($cell, html_entity_decode($value),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            elseif($type == "NUMBER") {
                $sheet->setCellValueExplicit($cell, $value,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            }
            elseif($type == "DATE") {
                $date = DateTime::createFromFormat('Y-m-d', $value);
                if($date !== FALSE){
                    $sheet->setCellValue($cell, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel( $date ));
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                }
                else{
                    $sheet->getCell($cell)->setValue($value);
                }
            }
            elseif($type == "TIME") {
                $time = DateTime::createFromFormat('H:i:s', $value);
                if($time !== FALSE){
                    $sheet->setCellValue($cell, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel( $time ));
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('h:mm');
                }
                else{
                    $sheet->getCell($cell)->setValue($value);
                }
            }
            elseif($type == "DATETIME") {
//                $sheet->setCellValue($cell, PHPExcel_Shared_Date::PHPToExcel( convertDate($value) ));
//                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);
                $sheet->getCell($cell)->setValue($value);
            }
            elseif($type == "SEPARATOR") {
                $sheet->getCell($cell)->setValue($value);
                $sheet->getStyle($cell)->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => array('rgb' => '99CCFF')
                        )
                    )
                );
            }
            else{
                $sheet->getCell($cell)->setValue(html_entity_decode($value));
            }
        }
        else{
            if(strpos($key, "@") === false){
                $sheet->getCell($cell)->setValue(html_entity_decode($value));
            }
            else{
                $nextCol = false;
            }
        }




            $sheet->getStyle($cell)->applyFromArray(
                array(
                    'borders'=> array(
                        'outline'=> array(
                            'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('rgb' => '000000')
                        )
                    )
                )
            );
        if($nextCol){
            $colNb++;
        }
    }
    $i++;
}

foreach ($columns as $column){
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

//var_dump($file_name);
//die();
//
ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0


//$spreadsheet = new Spreadsheet();
//$sheet = $spreadsheet->getActiveSheet();
//$sheet->setCellValue('A1', 'Hello World !');
//
//$writer = new Xlsx($spreadsheet);

$writer = new Xlsx($spreadsheet);
//$writer->save('hello world.xlsx');
$writer->save('php://output');

