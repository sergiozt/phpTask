<?php
/**
 * User: Sergij
 * Date: 14.01.14
 * Time: 19:57
 * */

class Csv_Reverse
{
    /**
     * Set default charset.
     *
     * @param string $charset Charset.
     *
     * @return mixed
     */
    public function __constructor($charset = 'UTF-8')
    {
        header('Content-Type: text/html;' . $charset);
    }

    /**
     * Get output csv.
     *
     * @param string $fileName File name.
     *
     * @return array
     */
    protected function _getOutputCsv($fileName)
    {
        return @fopen('input/' . $fileName, 'r') ? fopen('input/' . $fileName, 'r') : false;
    }

    /**
     * Set csv to array.
     *
     * @param mixed $file File.
     *
     * @return array
     */
    protected function _setCsvToArray($file)
    {
        while (($line = fgetcsv($file)) !== FALSE) {
            $resultArray[] = $line;
        }
        fclose($file);

        return $resultArray;
    }

    /**
     * Reverse array.
     *
     * @param array $array  Array of elements.
     * @param array $method Method of revert.
     *
     * @return array
     */
    protected function _reverseArray($array, $method)
    {
        $method =='column' ? : $array = array_reverse($array);
        foreach ($array as $subArray) {
            $outputArray[] = array_reverse($subArray);
        }

        return $outputArray;
    }

    /**
     * Set output csv file.
     *
     * @param string $inputFileName Input File.
     *
     * @param string $method  Method of revert.
     */
    public function setOutputCsv($inputFileName, $method = 'column')
    {
        $outputCsv = $this->_getOutputCsv($inputFileName);
        if (!$outputCsv) {
            echo 'File does not exist';
            exit();
        }
        $arrayFromCsv = $this->_setCsvToArray($outputCsv);
        $outputArray = $this->_reverseArray($arrayFromCsv, $method);
        $fp = fopen('output/file' . date("ymdhms_"). $method . '.csv', 'w');
        foreach ($outputArray as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        echo 'Convert was Successful';
    }
}

$inputFileName = 'file.csv';
$reverse = new Csv_Reverse();
$reverse->setOutputCsv($inputFileName);
