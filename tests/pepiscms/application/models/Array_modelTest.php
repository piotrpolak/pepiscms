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

function log_message($m)
{

}

class Array_modelTest extends PepisCMS_TestCase
{
    private static $tester;

    public function setUp()
    {
        require_once(VENDOR_PATH . 'codeigniter/framework/system/core/Model.php');
        require_once(PROJECT_BASE . 'pepiscms/application/core/PEPISCMS_Model.php');
        require_once(PROJECT_BASE . 'pepiscms/application/models/Array_model.php');
        require_once(PROJECT_BASE . 'pepiscms/application/libraries/DataGrid.php');
        require_once(__DIR__ . '/Array_model_tester.php');

        self::$tester = new Array_model_tester();
        self::$tester->setBasicFeed(array(
            ((object)array('id' => 1, 'firstName' => 'Tom', 'lastName' => 'Jones', 'dateOfBirth' => '1980-01-01', 'annualSalary' => 103000, 'lastActivityTimestamp' => '2015-04-01 05:01:01')),
            ((object)array('id' => 2, 'firstName' => 'Alice', 'lastName' => 'Jones', 'dateOfBirth' => '1981-11-01', 'annualSalary' => 103001, 'lastActivityTimestamp' => '2015-04-01 03:05:01')),
            ((object)array('id' => 3, 'firstName' => 'Bob', 'lastName' => 'Kowalski', 'dateOfBirth' => '1990-04-03', 'annualSalary' => 154300, 'lastActivityTimestamp' => '2014-02-20 17:05:01')),
            ((object)array('id' => 4, 'firstName' => 'Tom', 'lastName' => 'McDonald', 'dateOfBirth' => '1995-08-02', 'annualSalary' => 264320, 'lastActivityTimestamp' => '2011-12-12 14:45:31')),
        ));
    }

    public function test_getDistinctAssoc()
    {
        $this->assertEquals(4, count(self::$tester->getDistinctAssoc('id')));
        $this->assertEquals(3, count(self::$tester->getDistinctAssoc('firstName')));
        $this->assertEquals(3, count(self::$tester->getDistinctAssoc('lastName')));
        $this->assertEquals(4, count(self::$tester->getDistinctAssoc('dateOfBirth')));
    }

    public function test_getById()
    {
        $obj = self::$tester->getById(1);
        $this->assertEquals(1, $obj->id);

        $obj = self::$tester->getById(2);
        $this->assertEquals(2, $obj->id);

        $obj = self::$tester->getById(3);
        $this->assertEquals(3, $obj->id);

        $obj = self::$tester->getById(4);
        $this->assertEquals(4, $obj->id);
    }

    public function test_getAdvancedFeedOffset()
    {
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 1, 'id', 'ASC', array(), null);
        $this->assertEquals(1, count($feed));
        $this->assertEquals(1, $feed[0]->id);

        list($feed, $total) = self::$tester->getAdvancedFeed('*', 1, 1, 'id', 'ASC', array(), null);
        $this->assertEquals(1, count($feed));
        $this->assertEquals(2, $feed[0]->id);

        list($feed, $total) = self::$tester->getAdvancedFeed('*', 2, 1, 'id', 'ASC', array(), null);
        $this->assertEquals(1, count($feed));
        $this->assertEquals(3, $feed[0]->id);

        list($feed, $total) = self::$tester->getAdvancedFeed('*', 3, 1, 'id', 'ASC', array(), null);
        $this->assertEquals(1, count($feed));
        $this->assertEquals(4, $feed[0]->id);


        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 0, 'id', 'ASC', array(), null);
        $this->assertEquals(0, count($feed));

        // Wrong offset
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 99, 99, 'id', 'ASC', array(), null);
        $this->assertEquals(0, count($feed));

        // Negative offset
        list($feed, $total) = self::$tester->getAdvancedFeed('*', -1, 99, 'id', 'ASC', array(), null);
        $this->assertEquals(4, count($feed));

        list($feed, $total) = self::$tester->getAdvancedFeed('*', 1, 3, 'id', 'ASC', array(), null);
        $this->assertEquals(3, count($feed));
    }

    public function test_getAdvancedFeedFilterLike()
    {
        // Lower case
        $filters = array(
            array(
                'column' => 'firstName',
                'values' => array('tom'),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_LIKE
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(2, count($feed));

        // Upper case
        $filters = array(
            array(
                'column' => 'firstName',
                'values' => array('TOM'),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_LIKE
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(2, count($feed));

        // Partial
        $filters = array(
            array(
                'column' => 'firstName',
                'values' => array('lic'),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_LIKE
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));

        // Multiple like filters, partial
        $filters = array(
            array(
                'column' => 'firstName',
                'values' => array('tom'),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_LIKE
            ),
            array(
                'column' => 'lastName',
                'values' => array('ones'),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_LIKE
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));
    }

    public function test_getAdvancedFeedFilterEquals()
    {
        $filters = array(
            array(
                'column' => 'firstName',
                'values' => array('tom'),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(2, count($feed));

        $filters = array(
            array(
                'column' => 'firstName',
                'values' => array('om'),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(0, count($feed));
    }

    public function test_getAdvancedFeedFilterEqualsNumeric()
    {
        $filters = array(
            array(
                'column' => 'annualSalary',
                'values' => array(103001),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));

    }

    public function test_getAdvancedFeedFilterNotEquals()
    {
        $filters = array(
            array(
                'column' => 'firstName',
                'values' => array('alice'),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_NOT_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(3, count($feed));
    }

    public function test_getAdvancedFeedFilterNotEqualsNumeric()
    {
        $filters = array(
            array(
                'column' => 'annualSalary',
                'values' => array(103001),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_NOT_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(3, count($feed));
    }

    public function test_getAdvancedFeedFilterGreater()
    {
        $filters = array(
            array(
                'column' => 'annualSalary',
                'values' => array(103001),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_GREATER
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(2, count($feed));
    }

    public function test_getAdvancedFeedFilterGreaterOrEqual()
    {
        $filters = array(
            array(
                'column' => 'annualSalary',
                'values' => array(103001),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_GREATER_OR_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(3, count($feed));
    }

    public function test_getAdvancedFeedFilterLess()
    {
        $filters = array(
            array(
                'column' => 'annualSalary',
                'values' => array(103001),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_LESS
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));
    }

    public function test_getAdvancedFeedFilterLessOrEqual()
    {
        $filters = array(
            array(
                'column' => 'annualSalary',
                'values' => array(103001),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_LESS_OR_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(2, count($feed));
    }

    public function test_getAdvancedFeedFilterIn()
    {
        $filters = array(
            array(
                'column' => 'annualSalary',
                'values' => array(103001),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_IN
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));


        $filters = array(
            array(
                'column' => 'annualSalary',
                'values' => array(103001, 154300),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_IN
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(2, count($feed));


        // Some dummy elements
        $filters = array(
            array(
                'column' => 'annualSalary',
                'values' => array(103001, 154300, 999, 112),
                'type' => DataGrid::FILTER_BASIC,
                'condition' => DataGrid::FILTER_CONDITION_IN
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(2, count($feed));
    }


    public function test_getAdvancedFeedFilterEqualsDate()
    {
        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-01'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));

        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-01 14:35:22'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));


        // Empty
        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-02'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(0, count($feed));
    }

    public function test_getAdvancedFeedFilterEqualsTimestamp()
    {
        $filters = array(
            array(
                'column' => 'lastActivityTimestamp',
                'values' => array('2015-04-01'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(2, count($feed));

        // Empty
        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-02'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(0, count($feed));
    }

    public function test_getAdvancedFeedFilterNotEqualsDate()
    {
        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-01'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_NOT_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(3, count($feed));
    }

    public function test_getAdvancedFeedFilterNotEqualsTimestamp()
    {
        $filters = array(
            array(
                'column' => 'lastActivityTimestamp',
                'values' => array('2011-12-12'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_NOT_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(3, count($feed));
    }

    public function test_getAdvancedFeedFilterGreaterDate()
    {
        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-01'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_GREATER
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(3, count($feed));
    }

    public function test_getAdvancedFeedFilterGreaterTimestamp()
    {
        $filters = array(
            array(
                'column' => 'lastActivityTimestamp',
                'values' => array('2011-12-12'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_GREATER
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(3, count($feed));
    }

    public function test_getAdvancedFeedFilterGreaterOrEqualDate()
    {
        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-01'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_GREATER
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(3, count($feed));
    }

    public function test_getAdvancedFeedFilterGreaterOrEqualTimestamp()
    {
        $filters = array(
            array(
                'column' => 'lastActivityTimestamp',
                'values' => array('2011-12-12'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_GREATER_OR_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(4, count($feed));
    }

    public function test_getAdvancedFeedFilterLessDate()
    {
        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-01'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_LESS
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(0, count($feed));

        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-02'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_LESS
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));
    }

    public function test_getAdvancedFeedFilterLessTimestamp()
    {
        $filters = array(
            array(
                'column' => 'lastActivityTimestamp',
                'values' => array('2011-12-12'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_LESS
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(0, count($feed));

        $filters = array(
            array(
                'column' => 'lastActivityTimestamp',
                'values' => array('2011-12-13'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_LESS
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));
    }

    public function test_getAdvancedFeedFilterLessOrEqualDate()
    {
        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-01'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_LESS_OR_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));

        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-01'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_LESS_OR_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));
    }

    public function test_getAdvancedFeedFilterLessOrEqualTimestamp()
    {
        $filters = array(
            array(
                'column' => 'lastActivityTimestamp',
                'values' => array('2011-12-12'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_LESS_OR_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));

        $filters = array(
            array(
                'column' => 'lastActivityTimestamp',
                'values' => array('2011-12-13'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_LESS_OR_EQUAL
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(1, count($feed));
    }

    public function test_getAdvancedFeedFilterInDate()
    {
        $filters = array(
            array(
                'column' => 'dateOfBirth',
                'values' => array('1980-01-01', '1981-11-01', '1990-04-03', '1995-08-02'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_IN
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(4, count($feed));
    }

    public function test_getAdvancedFeedFilterInTimestamp()
    {
        $filters = array(
            array(
                'column' => 'lastActivityTimestamp',
                'values' => array('2015-04-01', '2014-02-20'),
                'type' => DataGrid::FILTER_DATE,
                'condition' => DataGrid::FILTER_CONDITION_IN
            )
        );
        list($feed, $total) = self::$tester->getAdvancedFeed('*', 0, 9999, 'id', 'ASC', $filters, null);
        $this->assertEquals(3, count($feed));
    }
}