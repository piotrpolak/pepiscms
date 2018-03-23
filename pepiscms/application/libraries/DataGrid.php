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
 * Datagrid utility class for rendering database views
 *
 * @version 1.2.1
 */
class DataGrid extends ContainerAware
{
    /**
     * Basic search filter
     */
    const FILTER_BASIC = 0;

    /**
     * Select filter, user can select one value at the time
     */
    const FILTER_SELECT = 1;

    /**
     * Date filter, user can pick up a data
     */
    const FILTER_DATE = 2;

    /**
     * Select multiple values filter, select
     */
    const FILTER_MULTIPLE_SELECT = 5;

    /**
     * Select multiple values filter, checkboxes
     */
    const FILTER_MULTIPLE_CHECKBOX = 6;

    /**
     * Hidden, not displayed
     */
    const FILTER_FORCED = 999;

    /*
     * Filter conditions
     */

    /** @deprecated as PepisCMS 0.2.4.4 */
    const FILTER_CONDITION_EQAL = 'eq';
    const FILTER_CONDITION_EQUAL = 'eq';
    const FILTER_CONDITION_NOT_EQUAL = 'ne';
    const FILTER_CONDITION_GREATER = 'gt';
    const FILTER_CONDITION_GREATER_OR_EQUAL = 'ge';
    const FILTER_CONDITION_LESS = 'lt';
    const FILTER_CONDITION_LESS_OR_EQUAL = 'le';
    const FILTER_CONDITION_LIKE = 'like';
    const FILTER_CONDITION_IN = 'in';

    /*
     * Available row colors
     */
    const ROW_COLOR_GREEN = 'green';
    const ROW_COLOR_BLUE = 'blue';
    const ROW_COLOR_ORANGE = 'orange';
    const ROW_COLOR_YELLOW = 'yellow';
    const ROW_COLOR_RED = 'red';
    const ROW_COLOR_GRAY = 'gray';
    const ROW_COLOR_DEEP_GREEN = 'deep_green';
    const ROW_COLOR_DEEP_BLUE = 'deep_blue';
    const ROW_COLOR_DEEP_RED = 'deep_red';

    /**
     * Table title
     *
     * @var string
     */
    private $title;

    /**
     * Base URL to which the filter/order values will be appended
     * @var string
     */
    private $base_url;

    /**
     * The object from which the data will be pulled
     * @var object
     */
    private $feed_object;

    /**
     * Associative array representing grid layout etc
     * @var array
     */
    private $definition;

    /**
     * Associative array representing filters
     * @var array
     */
    private $filter_definitions;

    /**
     * List of values for  manually applied filters
     * @var array
     */
    private $manually_applied_filters;

    /**
     * Number of items displayed per page
     * @var int
     */
    private $items_per_page;

    /**
     * Array containing field names
     * @var array
     */
    private $field_names;

    /**
     * Default order, column name and order (ASC or DESC)
     * @var array
     */
    private $default_order;

    /**
     * Is grid orderable?
     * @var bool
     */
    private $is_orderable;

    /**
     * Callback that takes row object and returns CSS class name
     * that will be associated to the row
     *
     * @var Callback
     */
    private $row_css_class_formatting_callback;

    /**
     * Helper variable indicating if external JS was already included
     * @var bool
     */
    private $is_date_js_included;

    /**
     * Is table head displayed?
     * @var bool
     */
    private $is_table_head_visible;

    /**
     * Is filter section displayed before DataGrid?
     * @var bool
     */
    private $is_filters_shown_before;

    /**
     * Is filter section displayed before DataGrid?
     * @var bool
     */
    private $is_filters_shown_after;

    /**
     * Default Constructor
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
        $this->clear();
        

        // The following code should be re-enabled when datafeed interface becomes compatible with multiple filters
        if (isset($_POST['reload_datagrid'])) {
            if (isset($_POST['filters_clear'])) {
                $_POST['filters'] = '';
            }

            @$url = self::generateLink($this->input->post('base_url'), 1, $this->input->post('order_by'),
                $this->input->post('order'), $this->input->post('filters'));
            redirect($url);
        }


        $this->load->language('datagrid');
        $this->load->helper('text');
    }

    /**
     * Resets the DataGrid internal data
     *
     * @return DataGrid
     */
    public function clear()
    {
        $this->title = false;
        $this->base_url = false;
        $this->feed_object = false;
        $this->definition = array();
        $this->filter_definitions = array();
        $this->manually_applied_filters = array();
        $this->items_per_page = 100;
        $this->field_names = array();
        $this->default_order = false;
        $this->is_orderable = true;
        $this->row_css_class_formatting_callback = false;
        $this->is_date_js_included = false;
        $this->is_table_head_visible = true;
        $this->is_filters_shown_before = true;
        $this->is_filters_shown_after = false;

        return $this;
    }

    /**
     * Sets whether the filters should be rendered AFTER the grid
     *
     * @param bool $is_filters_shown_before
     * @return DataGrid
     */
    public function setFiltersShownBeforeGrid($is_filters_shown_before = true)
    {
        $this->is_filters_shown_before = $is_filters_shown_before;
        return $this;
    }

    /**
     * Sets whether the filters should be rendered BEFORE the grid
     *
     * @param bool $is_filters_shown_after
     * @return DataGrid
     */
    public function setFiltersShownAfterGrid($is_filters_shown_after = true)
    {
        $this->is_filters_shown_after = $is_filters_shown_after;
        return $this;
    }

    /**
     * Sets whether the grid can be ordered by a column value
     *
     * @param bool $is_orderable
     * @return DataGrid
     */
    public function setOrderable($is_orderable)
    {
        $this->is_orderable = $is_orderable;
        return $this;
    }

    /**
     * Tells whether the grid can be ordered by a column value
     *
     * @return bool
     */
    public function isOrderable()
    {
        return $this->is_orderable;
    }

    /**
     * Shows or hides table head
     *
     * @param bool $is_table_head_visible
     * @return DataGrid
     */
    public function setTableHeadVisible($is_table_head_visible = true)
    {
        $this->is_table_head_visible = $is_table_head_visible;
        return $this;
    }

    /**
     * Tells whether table head is visible
     *
     * @return bool
     */
    public function isTableHeadVisible()
    {
        return $this->is_table_head_visible;
    }

    /**
     * Sets the table title
     *
     * @param string $title
     * @return DataGrid
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns the table title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the base url (url to which all the parameters are assigned), no trailing slash
     *
     * @param string $base_url no trailing slash
     * @return DataGrid
     */
    public function setBaseUrl($base_url)
    {
        $this->base_url = $base_url;
        return $this;
    }

    /**
     * Returns base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * Sets default order for DataGrid
     *
     * @param string $order_by
     * @param string $order
     * @return DataGrid
     */
    public function setDefaultOrder($order_by, $order = 'ASC')
    {
        $this->default_order = array('order_by' => $order_by, 'order' => strtoupper($order));
        return $this;
    }

    /**
     * Returns default order for DataGrid
     *
     * @return array
     */
    public function getDefaultOrder()
    {
        return $this->default_order;
    }

    /**
     * Returns current order
     *
     * @return array
     */
    public function getOrder()
    {
        $order_by = str_replace('-', '.', $this->input->getParam('order_by'));
        $order = $this->input->getParam('order');
        if (!$order_by && $this->default_order['order_by']) {
            return $this->getDefaultOrder();
        }

        return array('order_by' => $order_by, 'order' => strtoupper($order));
    }

    /**
     * Sets the number of items that will be displayed on a single page
     *
     * @param int $items_per_page
     *
     * @return DataGrid
     */
    public function setItemsPerPage($items_per_page)
    {
        $this->items_per_page = $items_per_page;
        return $this;
    }

    /**
     * Adds a column to the DataGrid
     *
     * @param $label
     * @param bool $field
     * @param bool $grid_formating_callback
     * @param bool $grid_css_class
     * @return DataGrid
     */
    public function addColumn($label, $field = false, $grid_formating_callback = false, $grid_css_class = false)
    {
        // If the first element is array, then setting field by definition
        if (is_array($label)) {
            return $this->addColumnByDefinition($label);
        }

        $column = array(
            'label' => $label,
            'field' => $field,
            'grid_formating_callback' => $grid_formating_callback,
            'grid_css_class' => $grid_css_class,
            'grid_is_orderable' => true
        );
        return $this->addColumnByDefinition($column);
    }

    /**
     * Returns field default configuration
     *
     * @return array
     */
    public function getFieldDefaults()
    {
        $defaults = array();
        $defaults['field'] = false; // Field name
        $defaults['label'] = false; // Field label
        $defaults['description'] = false; // Field description
        // Display options
        $defaults['show_in_form'] = true;  // Display in form?
        $defaults['show_in_grid'] = true;  // Display in grid?
        // Foreign key
        $defaults['foreign_key_relationship_type'] = 0; // FormBuilder::FOREIGN_KEY_ONE_TO_MANY;
        $defaults['foreign_key_table'] = false;
        $defaults['foreign_key_field'] = 'id';
        $defaults['foreign_key_label_field'] = 'id';
        $defaults['foreign_key_accept_null'] = false;
        $defaults['foreign_key_where_conditions'] = false; // TODO This is only implemented in FormBuilder, needs to be implemented in filters as well

        $defaults['foreign_key_junction_table'] = false;
        $defaults['foreign_key_junction_id_field_left'] = false;
        $defaults['foreign_key_junction_id_field_right'] = false;
        $defaults['foreign_key_junction_where_conditions'] = false;

        //
        // Input specific
        //
        $defaults['input_type'] = 0; //FormBuilder::TEXTFIELD;	// Input type, see FormBuilder constants
        $defaults['input_default_value'] = false; // Default value for field
        $defaults['values'] = false; // Values for to select among them, must be an associative array
        $defaults['validation_rules'] = 'required'; // Validation rules
        $defaults['input_is_editable'] = true;
        $defaults['input_group'] = false;
        $defaults['input_css_class'] = false;
        $defaults['options'] = array();

        // File upload
        $defaults['upload_complete_callback'] = false;
        $defaults['upload_path'] = ''; //$this->default_uploads_path;
        $defaults['upload_display_path'] = ''; //$this->default_upload_display_path;
        $defaults['upload_allowed_types'] = '*';
        $defaults['upload_encrypt_name'] = false;

        //
        // Grid specific
        //
        $defaults['grid_formating_callback'] = false;
        $defaults['grid_is_orderable'] = true;
        $defaults['grid_css_class'] = false;
        $defaults['filter_type'] = false;
        $defaults['filter_values'] = false; // It is not always the same for values
        $defaults['filter_condition'] = 'like';

        //
        // Excel cell format column
        //
        $defaults['excel_cell_format'] = false;
        $defaults['excel_header_format'] = false;

        // Autocomplete
        $defaults['autocomplete_source'] = '';

        return $defaults;
    }

    /**
     * Returns the default filter condition by filter type
     *
     * @param string $filter_type
     * @return string
     */
    private function getDefaultFilterConditionByFilterType($filter_type)
    {
        if ($filter_type == DataGrid::FILTER_SELECT) {
            return 'eq';
        }

        if ($filter_type == DataGrid::FILTER_MULTIPLE_SELECT) {
            return 'in';
        }

        // For most text fields
        return 'like';
    }

    /**
     * Adds a single column by definition
     *
     * @param array $column
     * @param bool $key
     * @return DataGrid
     */
    public function addColumnByDefinition($column, $key = false)
    {
        $defaults = $this->getFieldDefaults();
        if (!isset($column['filter_condition']) && isset($column['filter_type'])) {
            $defaults['filter_condition'] = $this->getDefaultFilterConditionByFilterType($column['filter_type']);
        }

        // Overwriting values of defaults
        foreach ($defaults as $name => $value) {
            if (isset($column[$name])) {
                $defaults[$name] = $column[$name];
            }
            unset($column[$name]); // Saving memory and preventing strange errors when some keys have null value
        }

        // Useful for debugging, prevents from using misspelled keys
        $unused_keys = array_keys($column);
        if (count($unused_keys)) {
            foreach ($unused_keys as $unused_key) {
                trigger_error('DataGrid definition contains unknown key: ' . $unused_key . '. Make sure you have submitted correct definition.', E_USER_NOTICE);
            }
        }

        // Generating label name if necessary
        $defaults['label'] = $defaults['label'] !== false ? $defaults['label'] : ucfirst(str_replace('_', ' ', $defaults['field']));

        // Adding filter
        if ($defaults['filter_type'] !== false) {
            $this->filter_definitions[$defaults['field'] . '_' . $defaults['filter_condition']] = array(
                'label' => $defaults['label'],
                'field' => $defaults['field'],
                'filter_values' => $defaults['filter_values'],
                'filter_type' => $defaults['filter_type'],
                'filter_condition' => $defaults['filter_condition'],
                'foreign_key_table' => $defaults['foreign_key_table'],
                'foreign_key_field' => $defaults['foreign_key_field'],
                'foreign_key_label_field' => $defaults['foreign_key_label_field'],
                'foreign_key_where_conditions' => $defaults['foreign_key_where_conditions'],
                'foreign_key_relationship_type' => $defaults['foreign_key_relationship_type'],
                'foreign_key_junction_table' => $defaults['foreign_key_junction_table'],
                'foreign_key_junction_id_field_left' => $defaults['foreign_key_junction_id_field_left'],
                'foreign_key_junction_id_field_right' => $defaults['foreign_key_junction_id_field_right']
            );
        }

        $this->field_names[] = $defaults['field'];

        // Adding field to the definition
        if ($key) {
            $this->definition[$key] = $defaults;
        } else {
            $this->definition[] = $defaults;
        }

        return $this;
    }

    /**
     * Sets columns by definition, this is the recommended way to configure DataGrid
     *
     * @param array $columns
     * @return DataGrid
     */
    public function setDefinition($columns)
    {
        $this->definition = array();

        foreach ($columns as $key => &$column) {
            // Make it work with associative
            if ($key && !is_numeric($key) && (!isset($column['field']) || $column['field'] === false)) {
                $column['field'] = $key;
            }

            $this->addColumnByDefinition($column, $key);
        }

        return $this;
    }

    /**
     * Returns columns' definition
     *
     * @return array
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Configures a filter
     *
     * @param string $label
     * @param bool $field
     * @param int $filter_type
     * @param bool $filter_values
     * @param bool $filter_condition
     * @return DataGrid
     */
    public function addFilter($label, $field = false, $filter_type = self::FILTER_BASIC, $filter_values = false, $filter_condition = false)
    {
        if (!is_array($label)) {
            if (!$filter_condition) {
                $filter_condition = $this->getDefaultFilterConditionByFilterType($filter_type);
            }
            $defaults = array(
                'label' => $label,
                'field' => $field,
                'filter_values' => $filter_values,
                'filter_type' => $filter_type,
                'filter_condition' => $filter_condition
            );
        } else {
            $defaults = $this->getFieldDefaults();
            if (!isset($label['filter_condition']) && isset($label['filter_type'])) {
                $defaults['filter_condition'] = $this->getDefaultFilterConditionByFilterType($label['filter_type']);
            }

            foreach ($label as $key => $value) {
                $defaults[$key] = $value;
            }
        }

        $this->filter_definitions[$defaults['field'] . '_' . $defaults['filter_condition']] = $defaults;

        return $this;
    }

    /**
     * Removes filter array
     *
     * @return DataGrid
     */
    public function removeFilters()
    {
        $this->filter_definitions = array();
        return $this;
    }

    /**
     * Removes filter by field name
     *
     * @param string $field_name
     * @return DataGrid
     */
    public function removeFilterByFieldName($field_name)
    {
        foreach ($this->filter_definitions as &$filter) {
            if ($filter['label'] == $field_name) {
                unset($filter);
            }
        }
        return $this;
    }

    /**
     * Returns filters' definition
     *
     * @return array
     */
    public function getFilterDefinition()
    {
        return $this->filter_definitions;
    }

    /**
     * Sets filters' definition
     *
     * @param array $filters_definition
     * @return DataGrid
     */
    public function setFilterDefinition($filters_definition)
    {
        $this->filter_definitions = $filters_definition;
        return $this;
    }

    /**
     * Applies filter
     *
     * @param string $field
     * @param string $filter_value
     * @param string $filter_condition
     * @return DataGrid
     */
    public function applyFilter($field, $filter_value, $filter_condition = DataGrid::FILTER_CONDITION_EQUAL)
    {
        $defaults = array(
            'label' => $field,
            'field' => $field,
            'filter_values' => false,
            'filter_type' => self::FILTER_FORCED,
            'filter_condition' => $filter_condition
        );

        foreach ($this->filter_definitions as $f_key => &$f_def) {
            if ($f_def['field'] == $field) {
                unset($this->filter_definitions[$f_key]);
            }
        }

        $this->filter_definitions[$defaults['field'] . '_' . $defaults['filter_condition']] = $defaults;
        $this->manually_applied_filters[$defaults['field'] . '_' . $defaults['filter_condition']] = $filter_value;
        return $this;
    }

    /**
     * Applies filter
     *
     * @param string $field
     * @param string $filter_value
     * @param string $filter_condition
     * @return DataGrid
     */
    public function setFilterValue($field, $filter_value, $filter_condition = 'eq')
    {
        $this->manually_applied_filters[$field . '_' . $filter_condition] = $filter_value;
        return $this;
    }

    /**
     * Sets the object from which the feed will be extracted, the feed must be of type DataFeedableInterface
     *
     * @param AdvancedDataFeedableInterface $feed_object
     * @return DataGrid
     */
    public function setFeedObject(AdvancedDataFeedableInterface $feed_object)
    {
        $this->feed_object = $feed_object;
        return $this;
    }

    /**
     * Returns feed object used by DataGrid
     *
     * @return Object
     */
    public function getFeedObject()
    {
        return $this->feed_object;
    }

    /**
     * Sets table, this is to be used along with the default Generic_model
     * The $where_conditions should be of form of associative array
     *
     * @param string $title
     * @param bool|array $where_conditions
     * @param bool $id_field
     * @return DataGrid
     */
    public function setTable($title, $where_conditions = false, $id_field = false)
    {
        $feed_object = clone $this->Generic_model;
        $feed_object->setTable($title);

        if (is_array($where_conditions)) {
            $feed_object->setWhere($where_conditions);
        }

        if ($id_field) {
            $feed_object->setIdFieldName($id_field);
        }

        $this->setFeedObject($feed_object);
        return $this;
    }

    /**
     * Returns filter pairs
     *
     * @return array
     */
    public function getFilterPairs()
    {
        $pairs = self::decodeFiltersString($this->input->getParam('filters'));

        // Overwriting only if does not exists
        foreach ($this->manually_applied_filters as $filter_key => $filter_value) {
            if (isset($pairs[$filter_key])) {
                continue;
            }
            $pairs[$filter_key] = $filter_value;
        }

        return $pairs;
    }

    /**
     * Returns filters to be applied in data grid
     *
     * @return array
     */
    public function getFiltersForDataFeed()
    {
        $pairs = $this->getFilterPairs();

        $applied_filter_values = array(); // For links and user interface
        $filters_for_data_feed = array(); // Do not use keys here, used to pass the values for the DataGrid, needs multiple values

        foreach ($pairs as $filter_key => $filter_value) {
            if (!isset($applied_filter_values[$filter_key]) || !is_array($applied_filter_values[$filter_key])) {
                // This happens only if the filter is not predefined
                if (!is_array($filter_value)) {
                    // Wrapping into array just for any case
                    $filter_value = array($filter_value);
                }

                $applied_filter_values[$filter_key] = $filter_value;
                continue;
            }

            $applied_filter_values[$filter_key][] = $filter_value;
        }


        // Generating structure for applying filters
        foreach ($applied_filter_values as $filter_key => $filter_values) {
            if (!isset($this->filter_definitions[$filter_key])) {
                continue;
            }

            if (isset($this->filter_definitions[$filter_key]['foreign_key_relationship_type']) && $this->filter_definitions[$filter_key]['foreign_key_relationship_type'] == FormBuilder::FOREIGN_KEY_MANY_TO_MANY) {
                $filter_values = $this->Generic_model->getDistinctAssoc($this->filter_definitions[$filter_key]['foreign_key_junction_id_field_left'], $this->filter_definitions[$filter_key]['foreign_key_junction_table'], false, array($this->filter_definitions[$filter_key]['foreign_key_junction_id_field_right'] => $filter_values[0])); // TODO MANY TO MANY will not work
                $filters_for_data_feed[$filter_key] = array('column' => 'id', 'values' => $filter_values, 'type' => $this->filter_definitions[$filter_key]['filter_type'], 'condition' => 'in');
            } else {
                $filters_for_data_feed[$filter_key] = array('column' => $this->filter_definitions[$filter_key]['field'], 'values' => $filter_values, 'type' => $this->filter_definitions[$filter_key]['filter_type'], 'condition' => trim($this->filter_definitions[$filter_key]['filter_condition']));
            }
        }

        return array($filters_for_data_feed, $applied_filter_values);
    }

    /**
     * Generates filters widget based on the definition and passed parameters
     *
     * @param bool $page
     * @param bool $order_by
     * @param bool $order
     * @param int $applied_filter_values
     * @return string
     */
    public function generateFilters($page = false, $order_by = false, $order = false, $applied_filter_values = -1)
    {
        $filters_output = '';

        if ($applied_filter_values == -1) {
            list($filters_for_data_feed, $applied_filter_values) = $this->getFiltersForDataFeed();
        }

        // Generating filters
        foreach ($this->filter_definitions as $filter_key => $filter_definition) {
            // No value for the filter, skipping
            if (!$filter_key || !$filter_definition['label'] || $filter_definition['filter_type'] == DataGrid::FILTER_FORCED) {
                continue;
            }

            // If there are no values or the values were not set
            if (!isset($filter_definition['filter_values']) || $filter_definition['filter_values'] === false) {
                if (($filter_definition['filter_type'] == DataGrid::FILTER_SELECT || $filter_definition['filter_type'] == DataGrid::FILTER_MULTIPLE_SELECT || $filter_definition['filter_type'] == DataGrid::FILTER_MULTIPLE_CHECKBOX)) {
                    // If there is a relationship
                    if ($filter_definition['foreign_key_table']) {
                        $filter_definition['filter_values'] = $this->Generic_model->getAssocPairs($filter_definition['foreign_key_field'], $filter_definition['foreign_key_label_field'], $filter_definition['foreign_key_table'], false, false, $filter_definition['foreign_key_where_conditions']);
                    } // In case there is no foreign key relationship and vales is not empty
                    elseif (isset($filter_definition['values']) && count($filter_definition['values']) > 0) {
                        $filter_definition['filter_values'] = $filter_definition['values'];
                    }
                }
            }

            $current_filter_values = (isset($applied_filter_values[$filter_key]) ? $applied_filter_values[$filter_key] : array(''));

            $filters_output .= "\n\n" . '<div class="datagrid_filter_box' . ($filter_definition['filter_type'] == DataGrid::FILTER_MULTIPLE_SELECT ? ' multiple' : '') . '"><label for="filters[' . $filter_key . ']" title="' . $filter_definition['label'] . '">' . word_limiter($filter_definition['label'], 5) . '</label>' . "\n";

            if ($filter_definition['filter_type'] == DataGrid::FILTER_SELECT) {
                //$filters_output .= '<!-- '.$current_filter_values[0].' -->' . "\n";
                $filters_output .= '<select name="filters[' . $filter_key . ']" id="filters[' . $filter_key . ']" class="text" >' . "\n";
                $filters_output .= "\t" . '<option value="">' . $this->lang->line('datagrid_any') . '</option>' . "\n";

                if ($filter_definition['filter_values']) { // This could be moved to another place
                    foreach ($filter_definition['filter_values'] as $id => $val) {
                        if (!$val && $id) {
                            $val = $id; // This is protection against empty keys or empty values
                        } elseif ($val && !$id && $id !== 0) {
                            $id = $val;
                        }

                        $filters_output .= "\t" . '<option value="' . $id . '" ' . ('' . $current_filter_values[0] === '' . $id ? ' selected="selected"' : '') . '>' . $val . '</option>' . "\n";
                    }
                }
                $filters_output .= '</select>' . "\n";
            } elseif ($filter_definition['filter_type'] == DataGrid::FILTER_MULTIPLE_SELECT) {
                $filters_output .= '<select multiple="multiple" size="5" name="filters[' . $filter_key . '][]" id="filters[' . $filter_key . '][]" class="text">' . "\n";
                $filters_output .= "\t" . '<option value="">' . $this->lang->line('datagrid_any') . '</option>' . "\n";

                foreach ($filter_definition['filter_values'] as $id => $val) {
                    if (!$val && $id) {
                        $val = $id; // This is protection against empty keys or empty values
                    } elseif ($val && !$id) {
                        $id = $val;
                    }

                    $filters_output .= "\t" . '<option value="' . $id . '" ' . (in_array('' . $id, $current_filter_values) ? ' selected="selected"' : '') . '>' . $val . '</option>' . "\n";
                }
                $filters_output .= '</select>' . "\n";
            } elseif ($filter_definition['filter_type'] == DataGrid::FILTER_MULTIPLE_CHECKBOX) {
                foreach ($filter_definition['filter_values'] as $id => $val) {
                    if (!$val && $id) {
                        $val = $id; // This is protection against empty keys or empty values
                    } elseif ($val && !$id) {
                        $id = $val;
                    }
                    $filters_output .= "\t" . '<div class="datagrid_multiple_checkbox_row"><input type="checkbox" value="' . $id . '" ' . (in_array('' . $id, $current_filter_values) ? ' checked="checked"' : '') . ' name="filters[' . $filter_key . '][]" id="filters[' . $filter_key . '][' . $id . ']"> <label for="filters[' . $filter_key . '][' . $id . ']">' . $val . '</label></div>' . "\n";
                }
            } elseif ($filter_definition['filter_type'] == DataGrid::FILTER_DATE) {
                if (!$this->is_date_js_included) {
                    $this->is_date_js_included = true;
                    $filters_output .= '<link href="pepiscms/3rdparty/jquery-ui/theme/smoothness/jquery-ui.custom.css" rel="stylesheet" type="text/css">' . "\n";
                    $filters_output .= '<script src="pepiscms/3rdparty/jquery-ui/jquery-ui.custom.min.js?v=' . PEPISCMS_VERSION . '"></script>' . "\n";
                }

                $filters_output .= '<div class="date_selector"><input type="text" name="filters[' . $filter_key . ']" id="filter_' . $filter_key . '" value="' . $current_filter_values[0] . '" class="text date" maxlength="8" size="8"><a href="#" id="filter_clear_' . $filter_key . '" title="' . $this->lang->line('datagrid_clear_date_filter') . '"><img src="pepiscms/theme/img/dialog/actions/delete_16.png" alt="remove"></a></div>' . "\n";
                $filters_output .= '<script>$("#filter_' . $filter_key . '").datepicker({dateFormat: "yy-mm-dd" });' . "\n";

                // The following line might cause some errors when using multiple forms on a single page
                $filters_output .= '$("#filter_clear_' . $filter_key . '").click(function(event) { event.stopPropagation();	event.preventDefault(); $("#filter_' . $filter_key . '").val(""); $(".filter_form").submit();});</script>' . "\n";
            } else {
                $filters_output .= '<input type="text" name="filters[' . $filter_key . ']" id="filter[' . $filter_key . ']" value="' . $current_filter_values[0] . '" class="text">' . "\n";
            }

            $filters_output .= '</div>' . "\n\n";
        }

        $output = '';
        if ($filters_output) {
            $output .= '<form method="POST" action="' . $this->base_url . '" class="filter_form" accept-charset="UTF-8">' . "\n";
            $output .= '<input type="hidden" name="reload_datagrid" value="1">' . "\n";
            $output .= '<input type="hidden" name="base_url" value="' . $this->base_url . '">' . "\n";
            $output .= '<input type="hidden" name="page" value="' . $page . '">' . "\n";
            $output .= '<input type="hidden" name="order_by" value="' . $order_by . '">' . "\n";
            $output .= '<input type="hidden" name="order" value="' . $order . '">' . "\n";
            $output .= $filters_output;
            $output .= '<div class="datagrid_filter_apply"><input type="submit" name="apply" value="' . $this->lang->line('datagrid_apply_filters') . '" class="button filter_apply"> <input type="submit" name="filters_clear" value="' . $this->lang->line('datagrid_clear_all_filters') . '" class="button filter_clear"></div>' . "\n";
            $output .= '</form>' . "\n\n";
        }

        return $output;
    }

    /**
     * Returns array containing data feed and filters info
     *
     * @param bool $extra_param
     * @param int $start
     * @param int $limit
     * @return array
     */
    public function getAdvancedFeed($extra_param = false, $start = 0, $limit = 999999)
    {
        $o = $this->getOrder();
        $order_by = $o['order_by'];
        $order = $o['order'];

        if ($order != 'DESC') {
            $order = 'ASC';
        }
        if (!$order_by && count($this->field_names) > 0) {
            $order_by = $this->field_names[0];
        }

        list($filters_for_data_feed, $applied_filter_values) = $this->getFiltersForDataFeed();
        return array_merge($this->feed_object->getAdvancedFeed('*', $start, $limit, $order_by, $order, $filters_for_data_feed, $extra_param), array($filters_for_data_feed, $applied_filter_values));
    }

    /**
     * The main method used for rendering the grid
     *
     * @param $extra_param mixed
     * @return string
     */
    public function generate($extra_param = false)
    {
        $output = '';

        $page = $this->input->getParam('page');
        if (!is_numeric($page) || $page < 1) {
            $page = 1;
        }
        $start = ($page - 1) * $this->items_per_page;

        $o = $this->getOrder();
        $order_by = $o['order_by'];
        $order = $o['order'];

        if ($order != 'DESC') {
            $order = 'ASC';
        }
        if (!$order_by && count($this->field_names) > 0) {
            $order_by = $this->field_names[0];
        }

        list($feed, $rowcount, $filters_for_data_feed, $applied_filter_values) = $this->getAdvancedFeed($extra_param = false, $start, $this->items_per_page);

        if ($this->is_filters_shown_before) {
            $output .= $this->generateFilters($page, $order_by, $order, $applied_filter_values);
        }

        $no_of_pages = floor(($rowcount - 1) / $this->items_per_page);

        //unset( $filters_for_data_feed ); // Saving some memory?

        $pagination = '';
        if ($no_of_pages > 0) {
            $pagination .= '<div class="datagrid_pagination">' . $this->lang->line('datagrid_page') . '' . "\n";
            ++$no_of_pages;
            if ($no_of_pages < 50) {
                for ($i = 1; $i <= $no_of_pages; $i++) {
                    $pagination .= '<a href="' . self::generateLink($this->base_url, $i, $order_by, $order, $applied_filter_values) . '"' . ($page == $i ? ' class="active"' : '') . '>' . $i . '</a> ';
                }
            } else {
                $print_min_dots = $print_max_dots = true;
                $min_start = 3;
                $max_start = $no_of_pages - 3;

                for ($i = 1; $i <= $min_start; $i++) {
                    $pagination .= '<a href="' . self::generateLink($this->base_url, $i, $order_by, $order, $applied_filter_values) . '"' . ($page == $i ? ' class="active"' : '') . '>' . $i . '</a> ';
                }

                $min = $page - 20;
                if ($min <= $min_start) {
                    $min = $min_start + 1;
                    $print_min_dots = false;
                }
                $max = $min + 40;
                if ($max >= $max_start) {
                    $max = $max_start - 1;
                    $print_max_dots = false;
                    $min = $max - 40;
                    // One more time
                    if ($min <= $min_start) {
                        $min = $min_start + 1;
                        $print_min_dots = false;
                    }
                }

                if ($print_min_dots) {
                    $pagination .= '... ';
                }

                for ($i = $min; $i <= $max; $i++) {
                    $pagination .= '<a href="' . self::generateLink($this->base_url, $i, $order_by, $order, $applied_filter_values) . '"' . ($page == $i ? ' class="active"' : '') . '>' . $i . '</a> ';
                }

                if ($print_max_dots) {
                    $pagination .= '... ';
                }

                for ($i = $max_start; $i <= $no_of_pages; $i++) {
                    $pagination .= '<a href="' . self::generateLink($this->base_url, $i, $order_by, $order, $applied_filter_values) . '"' . ($page == $i ? ' class="active"' : '') . '>' . $i . '</a> ';
                }
            }
            $pagination .= '</div>';
        }

        $output .= $pagination;

        $output .= '<div class="table_wrapper">' . "\n";
        if ($this->getTitle()) {
            $output .= '<h4>' . $this->getTitle() . '</h4>' . "\n";
        }
        $output .= '<table class="datagrid">' . "\n";


        // Rendering table head
        if ($this->isTableHeadVisible()) {
            $output .= "\t" . '<thead>' . "\n";
            foreach ($this->definition as &$column) {
                if (!$column['show_in_grid']) {
                    continue;
                }
                $column_order = ($column['field'] != $order_by || $order == 'DESC' ? 'ASC' : 'DESC');

                if ($this->isOrderable() && $column['field'] && $column['grid_is_orderable']) {
                    if ($column['field'] && $column['field'] == $order_by) {
                        $output .= "\t\t" . '<th class="' . strtolower($order) . ($column['grid_css_class'] ? ' ' . $column['grid_css_class'] : '') . '">';
                    } else {
                        if ($column['grid_css_class']) {
                            $output .= "\t\t" . '<th class="' . $column['grid_css_class'] . '">';
                        } else {
                            $output .= "\t\t" . '<th>';
                        }
                    }

                    $output .= '<a href="' . self::generateLink($this->base_url, $page, $column['field'], $column_order, $this->input->getParam('filters')) . '">'; //$filters
                    $output .= word_limiter($column['label'], 5);
                    $output .= '</a>';
                } else {
                    if ($column['grid_css_class']) {
                        $output .= "\t\t" . '<th class="' . $column['grid_css_class'] . '">';
                    } else {
                        $output .= "\t\t" . '<th>';
                    }

                    $output .= "\t\t" . '<span title="' . $column['label'] . '">';
                    $output .= word_limiter($column['label'], 5);
                    $output .= "\t\t" . '</span>';
                }
                $output .= '</th>' . "\n";
            }
            $output .= "\t" . '</thead>' . "\n";
        }

        $output .= "\t" . '<tbody>' . "\n";

        $is_feed_an_array = is_array($feed);

        if (count($feed) == 0 || !$is_feed_an_array) {
            $output .= "\t\t" . '<tr>' . "\n";
            $output .= "\t\t\t" . '<td colspan="' . count($this->definition) . '" class="no_items_to_display">' . $this->lang->line('datagrid_no_items_to_display') . '</td>';
            $output .= "\t\t" . '</tr>' . "\n";
        } else {
            // Solving foreign keys
            // It is worth to mention that it is ONLY quering for displayed data and not for all possible values - that is why it can not be used for filter_values
            foreach ($this->definition as &$column) {
                if (!$column['show_in_grid']) {
                    continue;
                }

                if (!is_array($column['values']) && $column['foreign_key_table']) {
                    if ($column['foreign_key_relationship_type'] == FormBuilder::FOREIGN_KEY_ONE_TO_MANY) {
                        $column['values'] = array();
                        if (!$column['field']) {
                            continue;
                        }

                        // Getting possible values
                        foreach ($feed as &$line) {
                            $column['values'][] = $line->{$column['field']};
                        }
                        $column['values'] = array_unique($column['values']);
                        $column['values'] = $this->Generic_model->getAssocPairs($column['foreign_key_field'], $column['foreign_key_label_field'], $column['foreign_key_table'], false, $column['values'], $column['foreign_key_where_conditions']);
                    } else {
                        $column['values'] = $this->Generic_model->getAssocPairs($column['foreign_key_field'], $column['foreign_key_label_field'], $column['foreign_key_table'], false, false, $column['foreign_key_where_conditions']);
                    }
                }
            }

            // Printing for each line
            foreach ($feed as &$line) {
                if ($this->row_css_class_formatting_callback) {
                    $class = call_user_func_array($this->row_css_class_formatting_callback, array(&$line));
                    if ($class) {
                        $output .= "\t\t" . '<tr class="' . $class . '">' . "\n";
                    } else {
                        $output .= "\t\t" . '<tr>' . "\n";
                    }
                } else {
                    $output .= "\t\t" . '<tr>' . "\n";
                }

                foreach ($this->definition as &$column) {
                    if (!$column['show_in_grid']) {
                        continue;
                    }
                    $content = '';
                    $field_name = $column['field'];

                    // Adding CSS class if specified
                    if ($column['grid_css_class']) {
                        $output .= "\t\t\t" . '<td class="' . $column['grid_css_class'] . '">';
                    } else {
                        $output .= "\t\t\t" . '<td>';
                    }

                    if ($field_name) {
                        if (isset($line->$field_name)) {
                            if ($column['values'] && isset($column['values'][$line->$field_name])) {
                                // Foreign key like
                                $content = $column['values'][$line->$field_name];
                            } else {
                                $content = $line->$field_name;
                            }
                        } elseif ($column['foreign_key_table']) {
                            if ($column['foreign_key_relationship_type'] == FormBuilder::FOREIGN_KEY_MANY_TO_MANY) {
                                // Building where conditions based on the user input and the object ID
                                // The following code comes from FormBuilder
                                // Since 0.2.4.3 $where_conditions is read from foreign_key_junction_where_conditions instead of foreign_key_where_conditions
                                // The elseif( is_array($field['foreign_key_where_conditions']) ) remains ONLY for backward compatibility
                                if (is_array($column['foreign_key_junction_where_conditions'])) {
                                    $where_conditions = $column['foreign_key_junction_where_conditions'];
                                } elseif (is_array($column['foreign_key_where_conditions'])) {
                                    $where_conditions = $column['foreign_key_where_conditions'];
                                } else {
                                    $where_conditions = array();
                                }


                                $d_values = array();
                                $d_ids = $this->Generic_model->getDistinctAssoc($column['foreign_key_junction_id_field_right'], $column['foreign_key_junction_table'], false, ($where_conditions + array($column['foreign_key_junction_id_field_left'] => $line->id)));
                                foreach ($d_ids as $d_id) {
                                    $d_values[] = $column['values'][$d_id];
                                }
                                $content = implode(', ', $d_values);
                            }
                        }
                    }

                    $content = htmlentities($content, ENT_COMPAT, 'UTF-8');

                    if (is_callable($column['grid_formating_callback'])) {
                        $content = call_user_func_array($column['grid_formating_callback'], array($content, &$line));
                    } elseif ($column['grid_formating_callback'] != false) {
                        show_error('Datagrid: Collumn formating callback for field <b>' . $column['field'] . '</b> is not callable.');
                    }

                    if ($content && !$column['grid_formating_callback'] && class_exists('FormBuilder') && $column['input_type'] == FormBuilder::IMAGE && file_exists($column['upload_path'] . $content)) {
                        // This will only work for backend apps
                        $content = '<img src="admin/ajaxfilemanager/absolutethumb/80/' . $column['upload_display_path'] . $content . '" alt="">';
                    }

                    $output .= $content;
                    $output .= '</td>' . "\n";
                }

                $output .= "\t\t" . '</tr>' . "\n\n";
                unset($line);
            }
        }
        $output .= "\t" . '</tbody>' . "\n";
        $output .= '</table>' . "\n";
        $output .= '</div>' . "\n\n";

        $output .= $pagination;

        if ($this->is_filters_shown_after) {
            $output .= $this->generateFilters($page, $order_by, $order, $applied_filter_values);
        }

        return $output;
    }

    /**
     * Encodes filters array into string
     *
     * @param array $filters
     * @return string
     */
    public static function encodeFiltersString($filters)
    {
        if (!is_array($filters)) {
            return false;
        }

        $filters_new = array();

        // Removing empty keys
        foreach ($filters as $name => &$value) {
            if (is_array($value)) {
                $filters_new[$name] = array();
                foreach ($value as &$item) {
                    $item = trim($item);
                    if ($item || '' . $item == '0') { // Preventing treating 0 as empty string
                        $filters_new[$name][] = $item;
                    }
                }
            } else {
                $value = trim($value);
                if ($value || '' . $value == '0') { // Preventing treating 0 as empty string
                    $filters_new[$name] = $value;
                }
            }
        }
        unset($filters);

        return base64_encode(http_build_query($filters_new));
    }

    /**
     * Encodes filters array into string
     *
     * @param array $filters
     * @return array
     */
    public static function decodeFiltersString($filters)
    {
        $filters = base64_decode($filters);
        if (!$filters) {
            return array();
        }
        parse_str($filters, $pairs);

        return $pairs;
    }

    /**
     * Sets table row css style formatting callback
     * The callback function takes the line object and must return string class
     *
     * @param callable|false $callback
     * @return bool
     */
    public function setRowCssClassFormattingFunction($callback)
    {
        if (is_callable($callback) || $callback === false) {
            $this->row_css_class_formatting_callback = $callback;
            return $this;
        }
        return $this;
    }

    /**
     * Returns table row css style formatting callback
     *
     * @return callable|bool
     */
    public function getRowCssClassFormattingFunction()
    {
        return $this->row_css_class_formatting_callback;
    }

    /**
     * Returns the list of manually applied filters
     *
     * @param bool|string $key
     * @return array
     */
    public function getManuallyAppliedFilters($key = false)
    {
        if (!$key) {
            return $this->manually_applied_filters;
        } else {
            return $this->manually_applied_filters[$key];
        }
    }

    /**
     * @param bool|string $key
     * @return array
     * @deprecated as PepisCMS 1.0.0
     */
    public function getManualyAppliedFilters($key = false)
    {
        return $this->getManuallyAppliedFilters($key);
    }

    /**
     * @param callable|false $callback
     * @return bool
     * @deprecated as PepisCMS 1.0.0
     */
    public function setRowCssClassFormatingFunction($callback)
    {
        trigger_error('DataGrid.setRowCssClassFormatingFunction() is deprecated and scheduled for deletion. Please use DataGrid.setRowCssClassFormattingFunction()', E_USER_DEPRECATED);
        return $this->setRowCssClassFormattingFunction($callback);
    }

    /**
     * @return callable|bool
     * @deprecated as PepisCMS 1.0.0
     */
    public function getRowCssClassFormatingFunction()
    {
        return getRowCssClassFormatingFunction();
    }

    /**
     * Returns possible field names
     *
     * @param bool|string $key
     * @return array
     * @deprecated as PepisCMS 1.0.0
     */
    public function getFieldNames($key = false)
    {
        if (!$key) {
            return $this->field_names;
        } else {
            return $this->field_names[$key];
        }
    }

    /**
     * Generates link for given parameters
     *
     * @param $base_url
     * @param int $page
     * @param bool $order_by
     * @param bool $order
     * @param bool|array $filters
     * @return string
     */
    private static function generateLink($base_url, $page = 1, $order_by = false, $order = false, $filters = false)
    {
        $url = $base_url;
        if ($page > 1) {
            $url .= '/page-' . $page;
        }
        if ($order_by) {
            $url .= '/order_by-' . str_replace('.', '-', $order_by);
        }
        if ($order) {
            $url .= '/order-' . $order;
        }

        if ($filters) {
            if (is_string($filters)) {
                $url .= '/filters-' . $filters;
            } else {
                $pairs = self::encodeFiltersString($filters);
                if (strlen($pairs) > 0) {
                    $url .= '/filters-' . $pairs;
                }
            }
        }

        return $url;
    }
}
