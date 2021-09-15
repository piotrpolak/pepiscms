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

function convert_accented_characters($input)
{
    return $input;
}

function remove_invisible_characters($input)
{
    return $input;
}

class SpreadsheetTest extends PepisCMS_TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;
    private $data;
    private $workingDirectory = '/var/tmp/pepiscmstest/'; // To be overwritten

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        require_once(PROJECT_BASE . 'pepiscms/application/libraries/Spreadsheet.php');
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->workingDirectory = '/var/tmp/pepiscmstest-' . rand(1000, 99999) . '-' . time() . '/';

        mkdir($this->workingDirectory);

        $this->data = array(
            array(
                'id' => 1,
                'age' => 20,
                'email' => 'test@email.pl',
                'interests' => 'a,b,c,d',
                'cities' => 'Bucharest; ; Romania',
                'Price IN PLN' => '44.67',
                'Price IN EUR' => '55,9',
            ),
            array(
                'id' => 2,
                'age' => 22,
                'email' => 'tester@email.pl',
                'interests' => 'l,l,f',
                'cities' => 'Warsaw; Marshovian; Poland',
                'Price IN PLN' => '54.98',
                'Price IN EUR' => '56,32',
            ),
            array(
                'id' => 2,
                'age' => 29,
                'email' => 'xbox@email.pl',
                'interests' => 'l,yr,f',
                'cities' => 'Warsaw; Poland',
                'Price IN PLN' => '53.58',
                'Price IN EUR' => '52,12',
            ),
        );
    }

    public function test_excel_write_and_read_empty_data()
    {
        $path = $this->workingDirectory . __METHOD__ . '.xls';
        $this->spreadsheet->generateExcel(array(), FALSE, $path, FALSE);
        $this->assertTrue(file_exists($path));

        $parsedData = $this->spreadsheet->parseExcel($path, TRUE, FALSE);
        $this->assertEquals(count($parsedData), 0);
    }

    public function test_csv_write_and_read_empty_data()
    {
        $path = $this->workingDirectory . __METHOD__ . '.csv';
        $this->spreadsheet->generateCSV(array(), FALSE, $path, FALSE);
        $this->assertTrue(file_exists($path));

        $parsedData = $this->spreadsheet->parseCSV($path, TRUE, FALSE);
        $this->assertEquals(count($parsedData), 0);
    }

    public function test_excel_write_and_read_empty_data_with_headers()
    {
        $path = $this->workingDirectory . __METHOD__ . '.xls';
        $this->spreadsheet->generateExcel(array(), array('name' => 'Name', 'year_born' => 'Year born'), $path, FALSE);
        $this->assertTrue(file_exists($path));

        $parsedData = $this->spreadsheet->parseExcel($path, TRUE, FALSE);
        $this->assertEquals(count($parsedData), 0);
    }

    public function test_csv_write_and_read_empty_data_with_headers()
    {
        $path = $this->workingDirectory . __METHOD__ . '.csv';
        $this->spreadsheet->generateCSV(array(), array('name' => 'Name', 'year_born' => 'Year born'), $path, FALSE);
        $this->assertTrue(file_exists($path));

        $parsedData = $this->spreadsheet->parseExcel($path, TRUE, FALSE);
        $this->assertEquals(count($parsedData), 0);
    }

    public function test_excel_write_and_read_no_normalization()
    {
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            return;
        }
        $path = $this->workingDirectory . __METHOD__ . '.xls';
        $this->spreadsheet->generateExcel($this->data, FALSE, $path, FALSE);
        $this->assertTrue(file_exists($path));

        $parsedData = $this->spreadsheet->parseExcel($path, TRUE, FALSE);
        $this->assertEquals(count($this->data), count($parsedData));

        for ($i = 0; $i < count($this->data); $i++) {
            foreach ($this->data[$i] as $key => $row) {
                $this->assertEquals($row, $parsedData[$i][$key]);
            }
        }
    }

    public function test_excel_write_and_read_with_normalization()
    {
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            return;
        }
        $path = $this->workingDirectory . __METHOD__ . '.xls';
        $this->spreadsheet->generateExcel($this->data, FALSE, $path, FALSE);
        $this->assertTrue(file_exists($path));

        $parsedData = $this->spreadsheet->parseExcel($path, TRUE, TRUE);
        $this->assertEquals(count($this->data), count($parsedData));

        for ($i = 0; $i < count($this->data); $i++) {
            foreach ($this->data[$i] as $key => $row) {
                $key = str_replace(' ', '_', trim(strtolower($key)));
                $this->assertEquals($row, $parsedData[$i][$key]);
            }
        }
    }

    public function test_csv_write_and_read_no_normalization()
    {
        $path = $this->workingDirectory . __METHOD__ . '.csv';
        $this->spreadsheet->generateCSV($this->data, FALSE, $path, FALSE);
        $this->assertTrue(file_exists($path));

        $parsedData = $this->spreadsheet->parseCSV($path, TRUE, FALSE);
        $this->assertEquals(count($this->data), count($parsedData));

        for ($i = 0; $i < count($this->data); $i++) {
            foreach ($this->data[$i] as $key => $row) {
                $this->assertEquals($row, $parsedData[$i][$key]);
            }
        }
    }

    public function test_csv_write_and_read_with_normalization()
    {
        $path = $this->workingDirectory . __METHOD__ . '.csv';
        $this->spreadsheet->generateCSV($this->data, FALSE, $path, FALSE);
        $this->assertTrue(file_exists($path));

        $parsedData = $this->spreadsheet->parseCSV($path, TRUE, TRUE);
        $this->assertEquals(count($this->data), count($parsedData));

        for ($i = 0; $i < count($this->data); $i++) {
            foreach ($this->data[$i] as $key => $value) {
                $key = str_replace(' ', '_', trim(strtolower($key)));
                $this->assertEquals($value, $parsedData[$i][$key]);
            }
        }
    }

    public function test_csv_write_and_read_first_line_not_as_keys()
    {
        $path = $this->workingDirectory . __METHOD__ . '.csv';
        $this->spreadsheet->generateCSV($this->data, FALSE, $path, FALSE);
        $this->assertTrue(file_exists($path));

        $parsedData = $this->spreadsheet->parseCSV($path, FALSE, FALSE);
        $this->assertEquals(count($this->data) + 1, count($parsedData));

        foreach ($this->data[0] as $key => $value) {
            $this->assertContains($key, $parsedData[0]);
        }

        $parsedData = $this->spreadsheet->parseCSV($path, FALSE, TRUE);
        $this->assertEquals(count($this->data) + 1, count($parsedData));

        foreach ($this->data[0] as $key => $value) {
            $this->assertContains($key, $parsedData[0]);
        }
    }

}