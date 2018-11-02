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

class PatternCompilerTest extends PepisCMS_TestCase
{
    public function test_compiles_keys()
    {
        $pattern = '{id} - {name} - {employee_function_1}';

        $obj = new stdClass();
        $obj->id = 1;
        $obj->name = "Peter";
        $obj->employee_function_1 = "Software developer";

        $this->assertEquals('1 - Peter - Software developer', PatternCompiler::compile($pattern, $obj));
    }

    public function test_leaves_empty_keys()
    {
        $pattern = '{id} - {name} - {employee_function_1}';

        $obj = new stdClass();
        $obj->id = 1;
        $obj->name = "Peter";

        $this->assertEquals('1 - Peter - {employee_function_1}', PatternCompiler::compile($pattern, $obj));
    }

    public function test_narrows_keys()
    {
        $pattern = '{id} - {name} - {employee_function_1}';

        $obj = new stdClass();
        $obj->id = 1;
        $obj->name = "Peter";
        $obj->employee_function_1 = "Software developer";

        $this->assertEquals('1 - {name} - {employee_function_1}', PatternCompiler::compile($pattern, $obj, array('id')));
    }
}
