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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Utility class for drawing charts using Google chart API
 *
 * @since 0.2.1
 *
 * @see http://code.google.com/apis/chart/interactive/docs/security_privacy.html
 * @see http://code.google.com/apis/ajax/playground/?type=visualization
 *
 */
class Google_chart_helper
{

    /**
     * List of used IDs
     *
     * @var array
     */
    private $used_ids = array();

    /**
     * Indicates whether the JS script was included
     *
     * @var bool
     */
    private $is_js_included = false;

    /**
     * Generates a pseudorandom ID used to name JavaScript objects
     *
     * @param string $prefix
     * @return string
     */
    private function generateId($prefix = 'chart')
    {
        while (true) {
            $id = $prefix . '_' . rand(10000, 99999);
            if (!in_array($id, $this->used_ids)) {
                $this->used_ids[] = $id;
                return $id;
            }
        }

        return false;
    }

    /**
     * Includes JavaScript, this function should be called once only
     *
     */
    private function includeJavaScripts()
    {
        if ($this->is_js_included) {
            return '';
        }
        $this->is_js_included = true;

        return '<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {packages: ["corechart"]});
</script>';
    }

    /**
     * Draws a simple chart, note data feed needs to be an associative array
     *
     * @param array $data_feed
     * @param string $title
     * @param int $width
     * @param int $height
     * @return string
     */
    public function drawSimplePieChart($data_feed, $title = '', $width = 1200, $height = 200)
    {
        $id = $this->generateId('pie_chart');

        $out = $this->includeJavaScripts();
        $out .= '<script type="text/javascript">' . "\n";
        $out .= 'function visualization_draw_' . $id . '() {' . "\n";
        $out .= '	var data = new google.visualization.DataTable();' . "\n";
        $out .= '	data.addColumn("string", "");' . "\n";
        $out .= '	data.addColumn("number", "");' . "\n";

        foreach ($data_feed as $name => $value) {
            $out .= '	data.addRow(["' . $name . '", ' . $value . ']);' . "\n";
        }

        $margin = 10;

        $chart_width = $width;
        if (!strpos($width, '%')) {
            $chart_width -= $margin * 2;
        }

        $chart_height = $height;
        if (!strpos($height, '%')) {
            $chart_height -= $margin * 2;
        }

        $out .= 'new google.visualization.PieChart(document.getElementById("' . $id . '")).draw(data, {
					width: "' . $width . '",
					height: "' . $height . '",
					chartArea: {top: ' . $margin . ', left: ' . $margin . ', right: ' . $margin . ', bottom: ' . $margin . ', width: "' . ($chart_width) . '", height: "' . ($chart_height) . '",},
					title: "' . $title . '",
					pieSliceText: "percentage",
				}
			);
		}' . "\n";


        $out .= 'google.setOnLoadCallback(visualization_draw_' . $id . ');' . "\n";
        $out .= '</script>' . "\n";
        $out .= '<div id="' . $id . '" style="padding: 10px; margin: 10px"></div>' . "\n";

        return $out;
    }

    /**
     * Draws a simple line chart
     *
     * If you want to draw multiple lines, the elements of associative
     * array $data_feed must be itself an array and $collumn2_desc should
     * contain array of names associated to these values
     *
     * Available data types: string (discrete), number, date, datetime or timeofday (continuous)
     *
     * @param array $data_feed
     * @param string $collumn1_desc
     * @param string $collumn2_desc
     * @param int $width
     * @param int $height
     * @param int $max_value
     * @param string $column1_data_type
     * @param string $column2_datatype
     * @return string
     */
    public function drawSimpleLineChart($data_feed, $collumn1_desc = '', $collumn2_desc = '', $width = 1200, $height = 200, $max_value = 10, $column1_data_type = "string", $column2_datatype = "number")
    {
        $id = $this->generateId('line_chart');

        $out = $this->includeJavaScripts();
        $out .= '<script type="text/javascript">' . "\n";
        $out .= 'function visualization_draw_' . $id . '() {' . "\n";
        $out .= '	var data = new google.visualization.DataTable();' . "\n";
        $out .= '	data.addColumn("' . $column1_data_type . '", "' . $collumn1_desc . '");' . "\n";
        if (is_array($collumn2_desc)) {
            foreach ($collumn2_desc as $col_desc) {
                $out .= '	data.addColumn("' . $column2_datatype . '", "' . $col_desc . '");' . "\n";
            }
        } else {
            $out .= '	data.addColumn("' . $column2_datatype . '", "' . $collumn2_desc . '");' . "\n";
        }

        $final_data_feed = array();
        if (isset($data_feed[0]) && is_array($data_feed[0])) {
            foreach ($data_feed[0] as $key => $value) {
                $final_data_feed[$key] = array();
                foreach ($data_feed as $f) {
                    $final_data_feed[$key][] = $f[$key];
                }
            }
        } else {
            $final_data_feed = $data_feed;
        }
        unset($data_feed);


        foreach ($final_data_feed as $name => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $out .= '	data.addRow(["' . $name . '", ' . $value . ']);' . "\n";
        }

        $out .= 'new google.visualization.LineChart(document.getElementById("' . $id . '")).draw(data, {curveType: "function",
					width: "' . $width . '",
					height: "' . $height . '",
					curveType: "none",
					vAxis: {maxValue: ' . $max_value . '}}
			);
		}' . "\n";


        $out .= 'google.setOnLoadCallback(visualization_draw_' . $id . ');' . "\n";
        $out .= '</script>' . "\n";
        $out .= '<div id="' . $id . '"></div>' . "\n";

        return $out;
    }

    /**
     * Draws a simple line chart
     *
     * If you want to draw multiple lines, the elements of associative
     * array $data_feed must be itself an array and $collumn2_desc should
     * contain array of names associated to these values
     *
     * Available data types: string (discrete), number, date, datetime or timeofday (continuous)
     *
     * @param array $data_feed
     * @param string $collumn1_desc
     * @param string $collumn2_desc
     * @param int $width
     * @param int $height
     * @param int $max_value
     * @param string $column1_data_type
     * @param string $column2_datatype
     * @return string
     */
    public function drawSimpleColumnChart($data_feed, $collumn1_desc = '', $collumn2_desc = '', $width = 1200, $height = 200, $max_value = 10, $column1_data_type = "string", $column2_datatype = "number")
    {
        $id = $this->generateId('column_chart');

        $out = $this->includeJavaScripts();
        $out .= '<script type="text/javascript">' . "\n";
        $out .= 'function visualization_draw_' . $id . '() {' . "\n";
        $out .= '	var data = new google.visualization.DataTable();' . "\n";
        $out .= '	data.addColumn("' . $column1_data_type . '", "' . $collumn1_desc . '");' . "\n";
        if (is_array($collumn2_desc)) {
            foreach ($collumn2_desc as $col_desc) {
                $out .= '	data.addColumn("' . $column2_datatype . '", "' . $col_desc . '");' . "\n";
            }
        } else {
            $out .= '	data.addColumn("' . $column2_datatype . '", "' . $collumn2_desc . '");' . "\n";
        }

        $final_data_feed = array();
        if (isset($data_feed[0]) && is_array($data_feed[0])) {
            foreach ($data_feed[0] as $key => $value) {
                $final_data_feed[$key] = array();
                foreach ($data_feed as $f) {
                    $final_data_feed[$key][] = $f[$key];
                }
            }
        } else {
            $final_data_feed = $data_feed;
        }
        unset($data_feed);


        foreach ($final_data_feed as $name => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $out .= '	data.addRow(["' . $name . '", ' . $value . ']);' . "\n";
        }

        $out .= 'new google.visualization.ColumnChart(document.getElementById("' . $id . '")).draw(data, {curveType: "function",
					width: "' . $width . '",
					height: "' . $height . '",
					curveType: "none",
					vAxis: {maxValue: ' . $max_value . '}}
			);
		}' . "\n";


        $out .= 'google.setOnLoadCallback(visualization_draw_' . $id . ');' . "\n";
        $out .= '</script>' . "\n";
        $out .= '<div id="' . $id . '"></div>' . "\n";

        return $out;
    }
}
