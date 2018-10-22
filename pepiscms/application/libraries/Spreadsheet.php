<?php

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See license.txt
 * @link                http://www.polak.ro/
 */

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as ExcelSpreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Spreadsheet class for generating and parsing both
 * Excel and CVS files
 *
 * @since 0.2.0
 */
class Spreadsheet extends ContainerAware
{
    const EXCEL_XLS = 'xls';
    const EXCEL_XLSX = 'xlsx';

    /**
     * Tells whether all dependencies are present and the feature is fully enabled.
     */
    public function isFullyEnabled()
    {
        return class_exists('\PhpOffice\PhpSpreadsheet\IOFactory');
    }

    /**
     * Key normalization helper
     *
     * @param $key
     * @return mixed|string
     */
    public function normalizeKey($key)
    {
        $this->load->helper('text');
        $key = convert_accented_characters($key);

        $key = str_replace(' ', '_', trim(strtolower($key)));

        // Only use CI helpers when in CI environment
        if ($this) {
            $key = remove_invisible_characters($key);
        }

        return $key;
    }

    /**
     * Parses Excel file into array
     *
     * @param string $path
     * @param bool $first_row_as_keys
     * @param bool $normalize_keys
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function parseExcel($path, $first_row_as_keys = true, $normalize_keys = true)
    {
        if (!$this->isFullyEnabled()) {
            throw new \RuntimeException("PhpSpreadsheet is not enabled. Please refer to README.md");
        }

        $data = array();
        $keys = array();

        $objPHPExcel = IOFactory::load($path);

        $worksheet = $objPHPExcel->getActiveSheet();

        $rows_count = 0 + $worksheet->getHighestRow();
        $columns_count = 0 + Coordinate::columnIndexFromString($worksheet->getHighestColumn());

        $i = 1;// Row index
        if ($first_row_as_keys) {
            for ($col = 0; $col < $columns_count; $col++) {
                $key = $worksheet->getCellByColumnAndRow($col, $i)->getValue();
                $keys[] = $key;
            }
            ++$i;

            if ($normalize_keys) {
                foreach ($keys as &$key) {
                    $key = $this->normalizeKey($key);
                }
            }
        } else {
            for ($k = 0; $k <= $rows_count; $k++) {
                $keys[] = $k;
            }
        }

        for ($row = $i; $row <= $rows_count; $row++) {
            $row_array = array();
            $has_values = false;
            for ($col = 0; $col < $columns_count; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                $row_array[$keys[$col]] = $value;
                if ($value) {
                    $has_values = true;
                }
            }
            if ($has_values) {
                array_push($data, $row_array);
            }
        }

        return $data;
    }

    /**
     * Parses CSV file into array
     *
     * @param string $path
     * @param bool $first_row_as_keys
     * @param bool $normalize_keys
     * @param bool|string $separator
     * @return array
     */
    public function parseCSV($path, $first_row_as_keys = true, $normalize_keys = true, $separator = false)
    {
        $lines = array();
        $keys = array();
        if (file_exists($path)) {
            $rowIndex = 0;
            if (($handle = fopen($path, "r")) !== false) {
                $line = fgets($handle);
                rewind($handle);

                if (!$separator) {
                    // Not really deterministic
                    $tabs_count = substr_count($line, "\t");
                    $comas_count = substr_count($line, ',');
                    $semicolon_count = substr_count($line, ';');

                    if ($tabs_count > $comas_count) {
                        $separator = "\t";
                        if ($semicolon_count > $tabs_count) {
                            $separator = ";";
                        }
                    } else {
                        $separator = ",";
                        if ($semicolon_count > $comas_count) {
                            $separator = ";";
                        }
                    }
                }

                while (($line = fgetcsv($handle, 4096, $separator)) !== false) {
                    if ($rowIndex++ == 0) {
                        if ($first_row_as_keys) {
                            $offset = 1;
                            $keys = array();
                            $ki = 0;
                            foreach ($line as $key) {
                                $key = trim(str_replace(array("\n", "\r"), '', $key));
                                if (!$key) {
                                    $key = $ki;
                                }
                                $keys[] = $key;
                                $ki++;
                            }

                            if ($normalize_keys) {
                                foreach ($keys as &$key) {
                                    $key = $this->normalizeKey($key);
                                }
                            }

                            continue;
                        } else {
                            for ($i = 0; $i < 99; $i++) {
                                $keys[] = $i;
                            }
                        }
                    }

                    $j = 0;
                    $row = array();

                    foreach ($line as &$item) {
                        $item = trim(str_replace(array("\n", "\r"), '', $item));
                        if (!isset($keys[$j])) {
                            continue;
                        }
                        $row[$keys[$j]] = $item;
                        $j++;
                    }
                    unset($line);
                    $lines[] = $row;
                }

                fclose($handle);
            }
        }

        return $lines;
    }

    /**
     * Generates Excel spreadsheet
     *
     * @param $feed
     * @param array|bool $headers
     * @param string|bool $file_name
     * @param bool $send
     * @param bool $print_headers
     * @param string $excel_type
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function generateExcel($feed, $headers = false, $file_name = false, $send = true, $print_headers = true, $excel_type = Spreadsheet::EXCEL_XLS)
    {
        if (!$this->isFullyEnabled()) {
            throw new \RuntimeException("PhpSpreadsheet is not enabled. Please refer to README.md");
        }

        // Create new PHPExcel object
        $excel = new ExcelSpreadsheet();
        $excel->getProperties()->setCreator("PepisCMS")
            ->setLastModifiedBy("PepisCMS")
            ->setTitle("")
            ->setSubject("")
            ->setDescription("")
            ->setKeywords("")
            ->setCategory("");

        // Setting default font
        $excel->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);

        // Setting styles for header
        $style_for_header = new Style();
        $style_for_header->applyFromArray(
            array(
                'font' => array(
                    'bold' => true,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'fill' => array(
                    'type' => Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCFFCC')
                ),
                'borders' => array(
                    'bottom' => array('style' => Border::BORDER_MEDIUM),
                    'right' => array('style' => Border::BORDER_THIN)
                )
            ));

        // Setting styles for data
        $style_for_data = new Style();
        $style_for_data->applyFromArray(
            array(
                'borders' => array(
                    'bottom' => array('style' => Border::BORDER_THIN),
                    'right' => array('style' => Border::BORDER_THIN)
                )
            ));

        // Working on a single sheet
        $worksheet = $excel->setActiveSheetIndex(0);

        // Create Table Headers
        $col = 0;
        $row = 1;

        // Finding header
        if (!$headers) {
            $headers = array();
            if (isset($feed[0])) {
                foreach ($feed[0] as $key => $value) {
                    $headers[$key] = $key;
                }
            }
        }

        // Checking if headers exist
        if (count($headers) > 0) {
            // Building headers table
            $headers_final = array();
            foreach ($headers as $key => $val) {
                if (!$val) {
                    $val = $key;
                }
                if (!$key || is_numeric($key)) {
                    $key = $val;
                }

                $headers_final[$key] = $val;
            }
            $headers = &$headers_final;

            // Printing header
            if ($print_headers) {
                foreach ($headers as $header) {
                    $worksheet->setCellValueByColumnAndRow($col, $row, $header);
                    $col++;
                }

                $excel->getActiveSheet()->duplicateStyle($style_for_header, Coordinate::stringFromColumnIndex(0) . '1:' . Coordinate::stringFromColumnIndex($col - 1) . '1');
                $row++;
            }

            // Setting column width
            for ($i = 0; $i < count($headers); $i++) {
                $excel->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth(20);
            }

            // Printing for each line
            if (count($feed)) {
                foreach ($feed as &$line) {
                    $col = 0;
                    foreach ($headers as $key => $label) {
                        if (isset($line->$key)) {
                            $worksheet->setCellValueByColumnAndRow($col, $row, $line->$key);
                        } elseif (is_array($line) && isset($line[$key])) {
                            $worksheet->setCellValueByColumnAndRow($col, $row, $line[$key]);
                        }

                        $col++;
                    }

                    // Applying styles
                    $excel->getActiveSheet()->duplicateStyle($style_for_data, Coordinate::stringFromColumnIndex(0) . $row . ':' . Coordinate::stringFromColumnIndex($col - 1) . $row);

                    $row++;
                    // Saving memory
                    unset($line);
                }
            }
        }


        // Writer type will be determined later
        $writer = null;

        // Determining file type and needed parser
        if ($excel_type == Spreadsheet::EXCEL_XLSX) {
            $extension = 'xlsx';
            $writer = IOFactory::createWriter($excel, 'Xlsx');
        } else {
            $extension = 'xls';
            $writer = IOFactory::createWriter($excel, 'Xls');
        }

        // Generating filename if not specified
        if (!$file_name) {
            $file_name = 'spreadsheet-' . date('Y-m-d-h-i-s') . '.' . $extension;
        }

        // Sending worksheet
        if ($send) {
            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $file_name . '"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $writer->save('php://output');
            exit;
        } else {
            // Saving worksheet to file
            return $writer->save($file_name);
        }
    }

    /**
     * Generates CSV spreadsheet
     *
     * @param array $feed
     * @param array|bool $headers
     * @param string|bool $file_name
     * @param bool $send
     * @param string|bool $separator
     * @param bool $print_headers
     * @return bool
     */
    public function generateCSV($feed, $headers = false, $file_name = false, $send = true, $separator = false, $print_headers = true)
    {
        if (!$separator) {
            $separator = ',';
        }

        if (!$file_name) {
            $file_name = 'spreadsheet-' . date('Y-m-d-h-i-s') . '.csv';
        }

        // Finding header
        if (!$headers) {
            $headers = array();
            if (isset($feed[0])) {
                foreach ($feed[0] as $key => $value) {
                    $headers[$key] = $key;
                }
            }
        }


        $file_contents = '';

        if (count($headers)) {
            $headers_final = array();
            foreach ($headers as $key => $val) {
                if (!$val) {
                    $val = $key;
                }
                if (!$key || is_numeric($key)) {
                    $key = $val;
                }

                $headers_final[$key] = $val;
            }
            $headers = &$headers_final;

            $col_count = count($headers);

            // Printing headers
            if ($print_headers) {
                $col = 1;
                foreach ($headers as $header) {
                    $file_contents .= $header;
                    if ($col_count != $col) {
                        $file_contents .= $separator;
                    }
                    $col++;
                }
            }

            $file_contents .= "\n";

            // Printing for each line
            foreach ($feed as &$line) {
                $col = 1;
                foreach ($headers as $key => $label) {
                    if (isset($line->$key)) {
                        $value = $line->$key;
                    } elseif (is_array($line) && isset($line[$key])) {
                        $value = $line[$key];
                    }

                    if (strpos($value, $separator) !== false) {
                        $value = '"' . str_replace('"', '\\"', $value) . '"';
                    }

                    $file_contents .= $value;

                    if ($col_count != $col) {
                        $file_contents .= $separator;
                    }
                    $col++;
                }
                $file_contents .= "\n";
                unset($line);
            }
        }


        if ($send) {
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename=' . $file_name);

            echo $file_contents;
            die();
        }

        return file_put_contents($file_name, $file_contents);
    }

    /**
     * Generates XML spreadsheet
     *
     * @param array $feed
     * @param array|bool $headers
     * @param string|bool $file_name
     * @param bool $send
     * @param string|bool $root_name
     * @param string|bool $item_name
     * @return bool
     */
    public function generateXML($feed, $headers = false, $file_name = false, $send = true, $root_name = false, $item_name = false)
    {
        if (!$file_name) {
            $file_name = 'spreadsheet-' . date('Y-m-d-h-i-s') . '.xml';
        }
        if (!$root_name) {
            $root_name = 'results';
        }
        if (!$item_name) {
            $item_name = 'item';
        }

        // Finding header
        if (!$headers) {
            $headers = array();
            foreach ($feed[0] as $key => $value) {
                $headers[$key] = $key;
            }
        }

        $headers_final = array();
        foreach ($headers as $key => $val) {
            if (!$val) {
                $val = $key;
            }
            if (!$key || is_numeric($key)) {
                $key = $val;
            }

            $val = trim(strtolower($val));
            $val = str_replace(array(' ', '-', '<', '>'), '_', $val);

            $headers_final[$key] = $val;
        }
        $headers = &$headers_final;

        $xml_writer = new XMLWriter();
        $xml_writer->openMemory();
        $xml_writer->setIndent(true);
        $xml_writer->setIndentString(' ');
        $xml_writer->startDocument('1.0', 'UTF-8');
        $xml_writer->startElement($root_name);

        // Printing for each line
        foreach ($feed as &$line) {
            $xml_writer->startElement($item_name);
            foreach ($headers as $key => $label) {
                $xml_writer->writeElement($label, $line->$key);
            }
            $xml_writer->endElement();
            unset($line);
        }
        $xml_writer->endElement();

        $file_contents = $xml_writer->outputMemory();

        if ($send) {
            header('Content-type: application/xml');
            header('Content-Disposition: attachment; filename=' . $file_name);

            echo $file_contents;
            die();
        }

        return file_put_contents($file_name, $file_contents);
    }
}
