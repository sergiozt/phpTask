<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sergij
 * Date: 14.01.14
 * Time: 19:57
 * */

class Csv_Reverse
{
    public function __constructor($charset = 'UTF-8'){
        header('Content-Type: text/html;' . $charset);
    }

    protected function getOutputCsv($fileName){
        return @fopen('input/' . $fileName, 'r') ? fopen('input/' . $fileName, 'r') : false;
    }

    protected function setCsvToArray($file){
        while (($line = fgetcsv($file)) !== FALSE) {
            $resultArray[] = $line;
        }
        fclose($file);
        return $resultArray;
    }

    protected function reverseArray($array, $method){
        $method =='column' ? : $array = array_reverse($array);
        foreach($array as $subArray){
            $outputArray[] = array_reverse($subArray);
        }
        return $outputArray;
    }

    public function setOutputCsv($inputFileName, $method = 'column'){
        $outputCsv = $this->getOutputCsv($inputFileName);
        if($outputCsv){
            $arrayFromCsv = $this->setCsvToArray($outputCsv);
            $outputArray = $this->reverseArray($arrayFromCsv, $method);
            $fp = fopen('output/file' . date("ymdhms_"). $method . '.csv', 'w');
            foreach ($outputArray as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);
            echo 'Convert was Successful';
        }else{
            echo 'File does not exist';
        }
    }
}
/**
 * Set input Csv file Name
 * setOutputCsv method can take two parameters:
 * change position of all elements or only for column(default in columns)
 *
 * */
$inputFileName = 'file.csv';
$reverse = new Csv_Reverse();
$reverse->setOutputCsv($inputFileName);
