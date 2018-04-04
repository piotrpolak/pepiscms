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
 * Generic model is the improved version od CodeIgniter model.
 * It implements methods specified by both Entitable and AdvancedDataFeedableInterface interfaces.
 *
 * In most cases this class should be extended and parametrized in the constructor but it is left as a non-abstract
 * for DataGrid and FormBuilder components that initialize and parametrize Generic_model "on-the-fly" using
 * prototype design pattern.
 *
 * @since 0.1.3
 */
class Generic_model extends PEPISCMS_Model implements EntitableInterface, MoveableInterface, AdvancedDataFeedableInterface
{
    /**
     * Database table name
     *
     * @var bool|string
     */
    private $table = false;

    /**
     * Associative array containing where conditions
     *
     * @var bool|array
     */
    private $where = false;

    /**
     * ID field name
     *
     * @var string
     */
    private $id_field_name = 'id';

    /**
     * List of names of accepted POST fields to be storied in database
     * @var array
     */
    private $accepted_post_fields = array();

    /**
     * Associative array storing mapping of filter names to database field names, often used with for join queries
     *
     * @var array
     */
    private $mapped_filter_fields = array();

    /**
     * List of fields that should be nullified when empty upon saving
     *
     * @var array
     */
    private $nullify_on_empty_post_fields = array();

    /**
     * Name of the field that should be marked upon deletion
     *
     * @var bool|string
     */
    private $delete_flag_field_name = false;

    /**
     * Value of the field that should be marked upon deletion
     *
     * @var mixed
     */
    private $delete_flag_field_value = false;

    /**
     * Whether to hide from feed the rows having deleted items (marked as deleted)
     *
     * @var bool
     */
    private $hide_from_feed_marked_as_deleted = false;

    /**
     * Returns current database object
     *
     * @return Database
     * @local
     */
    public function db()
    {
        return $this->db;
    }

    /**
     * Enables marking row as deleted
     *
     * @param string $delete_flag_field_name
     * @param string $delete_flag_field_value
     * @param bool $hide_from_feed_marked_as_deleted
     * @return Generic_model
     * @local
     */
    public function setFlagOnDelete($delete_flag_field_name, $delete_flag_field_value, $hide_from_feed_marked_as_deleted = false)
    {
        $this->delete_flag_field_name = $delete_flag_field_name;
        $this->delete_flag_field_value = $delete_flag_field_value;
        $this->hide_from_feed_marked_as_deleted = $hide_from_feed_marked_as_deleted;
        return $this;
    }

    /**
     * Sets the database connection to be used by the model,
     * database group must be defined in database config
     *
     * @param string $database_group
     * @return bool
     * @local
     */
    public function setDatabase($database_group)
    {
        require INSTALLATIONPATH . 'application/config/database.php';

        if (!isset($db[$database_group])) {
            return false;
        }

        $this->db = $this->load->database($db[$database_group], true);
        return true;
    }

    /**
     * Sets table name on which the model will operate.
     * Table name must be explicitly specified before the model is usable
     *
     * @param $table
     * @param bool $where_conditions
     * @return $this
     * @local
     */
    public function setTable($table, $where_conditions = false)
    {
        $this->table = $table;

        if ($where_conditions && is_array($where_conditions)) {
            $this->setWhere($where_conditions);
        }

        return $this;
    }

    /**
     * Returns table name
     *
     * @return string
     * @local
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Overwrites the name of the ID field
     * The default ID field is called id. Specify id field if your database table's id field is non standard
     * (example personal_number)
     *
     * @param string $id_field_name
     * @return $this
     * @local
     */
    public function setIdFieldName($id_field_name)
    {
        $this->id_field_name = $id_field_name;
        return $this;
    }

    /**
     * Returns the name of ID field
     *
     * @return string
     * @local
     */
    public function getIdFieldName()
    {
        return $this->id_field_name;
    }

    /**
     * Sets where conditions that will be used when obtaining the data for DataGrid.
     * Where conditions should be of the same form as passed to the Database query object.
     * The parameter must be an associative array where the keys specify the field names and the values are the desired values.
     *
     * @param array $where_conditions
     * @return $this
     * @local
     */
    public function setWhere($where_conditions)
    {
        $this->where = $where_conditions;
        return $this;
    }

    /**
     * Returns where conditions
     *
     * @return array
     * @local
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * Sets the list of accepted POST fields' names. The default saveById method
     * accepts only fields that were previously defined as accepted.
     * FormBuilder automatically sets the accepted post fields based on the defined inputs.
     *
     * @param array $fields_array
     * @return $this
     * @local
     */
    public function setAcceptedPostFields($fields_array)
    {
        $this->accepted_post_fields = $fields_array;
        return $this;
    }

    /**
     * Returns the list of mapped filter fields
     *
     * @return array
     * @local
     */
    public function getMappedFilterFields()
    {
        return $this->mapped_filter_fields;
    }

    /**
     * Appends accepted POST field. You can specify a single field (string) or a set of extra fields.
     * This method is handy when you need to append the list of accepted fields once the model/form has been defined,
     * for instance in a FormBuilder callback.
     *
     * @param array|string $field
     * @return $this
     * @local
     */
    public function addAcceptedPostField($field)
    {
        // Adding multiple fields
        if (is_array($field)) {
            // Merges when fields are already specified
            if (is_array($this->accepted_post_fields)) {
                $this->accepted_post_fields = array_merge($this->accepted_post_fields, $field);
            } else {
                // Overwrites when no fields were specified till this moment
                $this->accepted_post_fields = $field;
            }
        } else {
            $this->accepted_post_fields[] = $field;
        }
        return $this;
    }

    /**
     * Returns the list of accepted POST fields
     *
     * @return array
     * @local
     */
    public function getAcceptedPostFields()
    {
        return $this->accepted_post_fields;
    }

    /**
     * Sets the list of accepted POST fields that will be nullified when saved with an empty value
     *
     * @param array $fields_array
     * @return $this
     * @local
     */
    public function setNullifyOnEmptyPostFields($fields_array)
    {
        $this->nullify_on_empty_post_fields = $fields_array;
        return $this;
    }

    /**
     * Returns the list of accepted POST fields that will be nullified when saved with an empty value
     *
     * @return array
     * @local
     */
    public function getNullifyOnEmptyPostFields()
    {
        return $this->nullify_on_empty_post_fields;
    }

    /**
     * Sets the list of mapped filter fields.
     * Example: field name is ambiguous, you can specify name resolves to clients.name
     *
     * @param array $fields_array
     * @return bool
     * @local
     */
    public function setMappedFilterFields($fields_array)
    {
        if (!$fields_array || count($fields_array) == 0) {
            $this->mapped_filter_fields = array();
            return true;
        }
        foreach ($fields_array as $key => $value) {
            if (!$key || !$value) {
                continue;
            }

            $this->mapped_filter_fields[$key] = $value;
        }
        return true;
    }

    /**
     * Returns data feed compatible with DataFeedableInterface, array consisting of rowcount and the actual data
     *
     * @param string $columns
     * @param int $offset
     * @param int $rowcount
     * @param string $order_by_column
     * @param string $order
     * @param array $filters
     * @param Mixed $extra_param
     * @return array
     * @local
     */
    public function getAdvancedFeed($columns, $offset, $rowcount, $order_by_column, $order, $filters, $extra_param)
    {
        if (!$columns || $columns == '*') { // TODO Check if $columns == '*' is ok
            $columns = $this->getTable() . '.*';
        }

        $this->db->from($this->getTable());

        if ($this->getWhere()) {
            $this->applyWhere($this->db(), $this->getWhere());
        }

        if ($this->hide_from_feed_marked_as_deleted) {
            $this->db->where($this->delete_flag_field_name . ' !=', $this->delete_flag_field_value);
        }

        $filters = $this->mapFilterFields($filters);
        self::applyFilters($this->db(), $filters);
        // Important, this must be after applying filters but before setting anything else
        $db2 = clone $this->db();

        $this->db->select($columns . ', ' . $this->getTable() . '.' . $this->getIdFieldName() . ' AS ' . $this->getIdFieldName())->limit($rowcount, $offset);
        $db2->select('count(*) AS rowcount');

        // Order by is not important for rowcount and in some situations can break the rowcount query
        if ($order_by_column) {
            $order_by_column = $this->mapFilterFieldName($order_by_column);
            $this->db->order_by($order_by_column, $order);
        }

        $query = $this->db->get();
        $query2 = $db2->get();

        $rowcount = 0;
        if ($query2) {
            $row = $query2->row();
            if ($row) {
                $rowcount = $row->rowcount;
            }
        }

        if ($query) {
            return array($query->result(), $rowcount);
        } else {
            return array(array(), 0);
        }
    }

    /**
     * Applies filter field names mappings to filter array
     *
     * @param array $filters
     * @return array
     * @local
     */
    protected function mapFilterFields($filters)
    {
        $out = array();
        foreach ($filters as $filter) {
            $out_element = $filter;
            if (isset($this->mapped_filter_fields[$filter['column']])) {
                $out_element['column'] = $this->mapped_filter_fields[$filter['column']];
            }
            $out[] = $out_element;
        }
        return $out;
    }

    /**
     * Applies filter field name mappings to a filter field name
     *
     * @param string $field_name
     * @return string
     * @local
     */
    protected function mapFilterFieldName($field_name)
    {
        if (isset($this->mapped_filter_fields[$field_name])) {
            return $this->mapped_filter_fields[$field_name];
        }

        // Default
        return $field_name;
    }

    /**
     * Static method that applies filters specified in the DataGrid format upon the specified DB object.
     * This method is very useful when you write your own getAdvancedFeed method and you want it to be compaible with the filers.
     *
     * @param object $db
     * @param array $filters
     * @local
     */
    public static function applyFilters($db, $filters)
    {
        $allowed_conditions = array(
            'eq' => '=',
            'ne' => '!=',
            'gt' => '>',
            'ge' => '>=',
            'lt' => '<',
            'le' => '<=',
            'like' => 'like',
            'in' => 'in'
        );

        foreach ($filters as $filter) {
            $count_values = count($filter['values']);

            // No value - no filtering
            if (!$count_values) {
                continue;
            }

            // For condition type IN or multiple values we do where_in
            if ($filter['condition'] == 'in' || $count_values > 1) {
                $db->where_in($filter['column'], $filter['values']);
                continue;
            }

            // We need to filter the parameter. No joke.
            $condition = '';
            if (isset($allowed_conditions[$filter['condition']])) {
                $condition = ' ' . $allowed_conditions[$filter['condition']] . ' ';
            }

            // We allays pass an array (for simplicity) so we need to extract the exact value
            $query = $filter['values'][0];
            $escape = true;

            switch ($filter['type']) {
                case DataGrid::FILTER_SELECT:
                    $comparison_method = 'where';
                    break;

                case DataGrid::FILTER_MULTIPLE_SELECT:
                    $comparison_method = 'where';
                    break;

                case DataGrid::FILTER_DATE:
                    $filter['column'] = "DATE_FORMAT(" . $filter['column'] . ",'%Y-%m-%d')"; // We extract the date only
                    $query = '\'' . $db->escape_str($query) . '\'';
                    $escape = false;
                    $comparison_method = 'where';
                    break;

                default:
                    $comparison_method = 'where';
                    $query = $db->escape_str($query);

                    if (!$condition || $filter['condition'] == 'like') {
                        $comparison_method = 'like';
                        $condition = ''; // Preventing errors
                    } else {
                        if (is_string($query)) {
                            $query = '"' . $query . '"'; // Wrapping string
                        }
                    }

                    $escape = false;
                    break;
            }

            // We add were condition to query builder
            $db->$comparison_method($filter['column'] . $condition, $query, $escape);
        }
    }

    /**
     * Returns a list of all elements
     *
     * @param array $where_conditions
     * @param string $fields
     * @return array
     * @local
     */
    public function getAll($where_conditions = array(), $fields = '*')
    {
        return $this->db->select($fields)
            ->where($where_conditions)
            ->get($this->getTable())
            ->result();
    }

    /**
     * Returns object by ID
     *
     * @param mixed $id
     * @param bool|string $fields
     * @return object
     * @local
     */
    public function getById($id, $fields = false)
    {
        if ($fields === false) {
            $fields = $this->getTable() . '.*, '; // Selecting all fields
        } elseif ($fields == $this->getIdFieldName()) {
            $fields = ''; // This is selected anyway
        } else {
            $fields = $fields . ', '; // Just appending the coma
        }

        return $this->db->select($fields . '' . $this->getTable() . '.' . $this->getIdFieldName() . ' AS ' . $this->getIdFieldName())
            ->where($this->getTable() . '.' . $this->getIdFieldName(), $id)
            ->limit(1)
            ->get($this->getTable())
            ->row();
    }

    /**
     * Deletes by id
     *
     * @param mixed $id
     * @return bool
     * @local
     */
    public function deleteById($id)
    {
        $this->db->where($this->getIdFieldName(), $id)->from($this->getTable());

        if ($this->delete_flag_field_name) {
            return $this->db->set($this->delete_flag_field_name, $this->delete_flag_field_value)->update();
        }

        return $this->db->delete();
    }

    /**
     * Saves by id, $data must be an associative array and it will be filtered
     * using accepted post fields
     *
     * @param mixed $id
     * @param array $data
     * @return bool
     * @local
     */
    public function saveById($id, $data)
    {
        // Checking data accepted fields
        $accepted_post_fields = $this->getAcceptedPostFields();
        if (!is_array($accepted_post_fields) || count($accepted_post_fields) == 0) {
            trigger_error('Method Generic_model::saveById() has no accepted fields defined. Make sure there are accepted fields set.');
            return false;
        }

        // Reading fields that should be nullified when empty
        $nullify_on_empty_post_fields = $this->getNullifyOnEmptyPostFields();

        // The filtered output array
        $data_to_save = array();

        foreach ($data as $_param_name => $_param_value) {
            // Checking if the field is allowed
            if (!in_array($_param_name, $accepted_post_fields)) {
                continue;
            }

            // Checking if the field should be nullified
            if (in_array($_param_name, $nullify_on_empty_post_fields) && !$_param_value) {
                $_param_value = null;
            }

            // Building save array
            $data_to_save[$_param_name] = $_param_value;
        }

        // Checking if there is anything to be saved
        if (!count($data_to_save)) {
            trigger_error('Method Generic_model::saveById() has no fields to save. Make sure there are accepted fields set.');
            return false;
        }

        // Journaling if case
        if ($id && $this->getJournalingIsEnabled()) {
            $this->journalingPersistPreSave($id, $data_to_save);
        }

        // Building query
        $this->db->set($data_to_save);

        if ($id !== false && $id !== '') {
            // Updating for existing records
            $this->db->where($this->getIdFieldName(), $id);
            $success = $this->db->update($this->getTable());
        } else {
            // Inserting for new records
            $success = $this->db->insert($this->getTable());
        }

        return $success;
    }

    /**
     * This method is used to move elements of a list. It is used for changing the order of menu elements in page controller.
     *
     * Dont try to understand how it works, even for the author is looks like magic after some months.
     * The following method takes all the items belonging to the same "group"
     * (all items having the same value of constraint_field),
     * moves the elements (swaps the item_order field) and normalizes the item_order values.
     *
     * @param int $id
     * @param string $direction
     * @param bool $table
     * @param bool $constraint_field_name
     * @param string $item_order_field_name
     * @param string $id_field_name
     * @return bool
     * @local
     */
    public function move($id, $direction, $table = false, $constraint_field_name = false, $item_order_field_name = 'item_order', $id_field_name = 'id')
    {
        // When no table is specified, taking table from the object
        if (!$table) {
            $table = $this->getTable();
        }

        // Protection against executing query on no table
        if (!$table) {
            trigger_error('Method Generic_model::move() has no database table specified.');
            return false;
        }

        // Lets set up the initial values if there are any nulls
        $this->db->set($item_order_field_name, '0')->where($item_order_field_name . ' IS NULL', false, false)->update($table);


        $relations = array('down' => '>=', 'up' => '<=');
        if (!isset($relations[$direction])) {
            trigger_error('Method Generic_model::move() direction accepts only up and down values.');
            return false;
        }
        $relation = $relations[$direction];

        // Getting item order
        if ($constraint_field_name) {
            $this->db->select($constraint_field_name . ', ' . $item_order_field_name);
        } else {
            $this->db->select($item_order_field_name);
        }
        $row = $this->db->where($id_field_name, $id)->get($table)->row();

        if ($constraint_field_name) {
            $constraint_field_name_value = $row->$constraint_field_name;
        }
        $item_order = $row->$item_order_field_name;
        if (!$item_order) {
            $item_order = 0;
        }

        // Getting the number of elements
        $this->db->select('count(*) as count')->where($item_order_field_name . ' ' . $relation, $item_order)->where($id_field_name . ' !=', $id);
        if ($constraint_field_name) {
            $this->db->where($constraint_field_name, $constraint_field_name_value);
        }
        $row = $this->db->get($table)->row();

        // There are no other elements
        if ($row->count == 0) {
            return false;
        }

        // Getting all the elements to be ordered
        $this->db->select($id_field_name . ', ' . $item_order_field_name)->order_by($item_order_field_name);
        if ($constraint_field_name) {
            $this->db->where($constraint_field_name, $constraint_field_name_value);
        }
        $result = $this->db->get($table)->result();

        $update_map = array();

        if ($direction == 'down') {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]->$item_order_field_name != $i) {
                    // Saving some queries
                    if ($result[$i]->$item_order_field_name != $i) {
                        $update_map[$result[$i]->$id_field_name] = $i;
                    }
                }

                // Swaping lines
                if ($result[$i]->$id_field_name == $id) {
                    $update_map[$id] = $i + 1;
                    $update_map[$result[$i + 1]->$id_field_name] = $i;
                    $i++;
                }
            }
        } else {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]->$item_order_field_name != $i) {
                    // Saving some queries
                    if ($result[$i]->$item_order_field_name != $i) {
                        $update_map[$result[$i]->$id_field_name] = $i;
                    }
                }

                // Swaping lines
                if ($result[$i]->$id_field_name == $id) {
                    $update_map[$id] = $i - 1;
                    $update_map[$result[$i - 1]->$id_field_name] = $i;
                }
            }
        }

        // We will order the elements anyway, but when there is no difference we will return false
        $success = true;
        if ($update_map[$id] == $item_order) {
            $success = false;
        }

        // Now updating
        foreach ($update_map as $idem_id => $item_order) {
            $this->db->set($item_order_field_name, $item_order)->where($id_field_name, $idem_id)->update($table);
        }

        return $success;
    }

    /**
     * Gets distinct values from a table's collumn.
     * If table parameter is not specified, it will take it from the instance $table variable.
     * You can predefine the return array by specifying $pairs.
     *
     * @param string $column
     * @param bool $table
     * @param bool $pairs
     * @param bool $where_conditions
     * @return array|bool
     * @local
     */
    public function getDistinctAssoc($column, $table = false, $pairs = false, $where_conditions = false)
    {
        if (!$table) {
            $table = $this->getTable();
        }
        // It needs to be automatically appended with the apostrophes in order to avoid wrong queries when the first letter of field name is capital
        $this->db->select('DISTINCT(`' . $column . '`) as distinct_value', false)->order_by($column);

        if ($where_conditions) {
            $this->applyWhere($this->db, $where_conditions);
        }

        $query = $this->db->get($table);
        if (!$query) {
            return false;
        }

        $result = $query->result();

        $output = array();
        if (count($result) > 0) {
            if (!$pairs) {
                foreach ($result as $line) {
                    $output[$line->distinct_value] = $line->distinct_value;
                }
            } else {
                foreach ($result as $line) {
                    if (isset($pairs[$line->distinct_value])) {
                        $output[$line->distinct_value] = $pairs[$line->distinct_value];
                        continue;
                    }

                    $output[$line->distinct_value] = $line->distinct_value;
                }
            }
        }
        return $output;
    }

    /**
     * Applies where conditions to the specified database object
     *
     * @param Database $db
     * @param array $where_conditions
     * @local
     */
    protected function applyWhere($db, $where_conditions)
    {
        if ($where_conditions) {
            foreach ($where_conditions as $key => $condition) {
                if (!$key || is_numeric($key)) {
                    $db->where($condition, false, false);
                } elseif ($condition === null) {
                    $db->where($key, null, false);
                } elseif (!$condition && $condition !== 0 && $condition !== '0') { // Fix as 0.2.2
                    $db->where($key, false, false);
                } else {
                    $db->where($key, $condition);
                }
            }
        }
    }

    /**
     * Get an associative array build with keys found in $key_column_name column and values found
     * in $value_column_name. You can specify the initial array as $imitial_array
     *
     * @param string $key_column_name
     * @param string $value_column_name
     * @param bool $table
     * @param bool $imitial_array
     * @param bool $possible_keys
     * @param bool $where_conditions
     * @return array|bool
     * @local
     */
    public function getAssocPairs($key_column_name, $value_column_name, $table = false, $imitial_array = false, $possible_keys = false, $where_conditions = false)
    {
        if (!$table) {
            $table = $this->getTable();
        }

        if (!$imitial_array) {
            $imitial_array = array();
        }

        $this->db->select($key_column_name . ', ' . $value_column_name)->order_by($value_column_name);
        if ($possible_keys !== false && count($possible_keys) > 0) {
            $this->db->where_in($key_column_name, $possible_keys);
        }

        $this->applyWhere($this->db, $where_conditions);

        $query = $this->db->get($table);

        if ($query) {
            $result = $query->result();
            foreach ($result as $line) {
                $imitial_array[$line->$key_column_name]
                    = $line->$value_column_name;
            }
        }

        return $imitial_array;
    }

    // TODO implement an interface

    /**
     * @var bool
     */
    private $journaling_is_enabled = false;

    /**
     * @var null|string
     */
    private $journaling_table = null;

    /**
     * @var null|string
     */
    private $journaling_tag = null;

    /**
     * @var null|callback
     */
    private $journaling_serialization_method = null;

    /**
     * @var null|callback
     */
    private $journaling_unserialization_method = null;

    /**
     * @var array
     */
    private $journaling_include_fields = array();

    /**
     * @var array
     */
    private $journaling_exclude_fields = array();

    /**
     * Enables journaling for given entity
     *
     * @param string|bool $journaling_table
     * @param array $include_fields
     * @param array $exclude_fields
     * @param string|null $tag
     * @param callable|null $serialization_method
     * @param callable|null $unserialization_method
     * @return bool
     * @local
     */
    public function enableJournaling($journaling_table = false, $include_fields = array(), $exclude_fields = array(), $tag = null, $serialization_method = null, $unserialization_method = null)
    {
        if (!$tag) {
            $tag = $this->getTable();
        }

        $this->setJournalingIsEnabled(true);
        $this->setJournalingTable($journaling_table);
        $this->setJournalingIncludeFields($include_fields);
        $this->setJournalingTag($tag);
        $this->setJournalingSerializationMethod($serialization_method);
        $this->setJournalingUnserializationMethod($unserialization_method);

        return true;
    }

    /**
     * Pre-saves journaling data
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @local
     */
    protected function journalingPersistPreSave($id, $data)
    {
        if (!$id || !$this->getJournalingIsEnabled()) {
            return false;
        }

        $row = $this->getById($id);
        if (!$row) {
            return false;
        }

        $row = (array)$row;

        $use_include_fields = is_array($this->getJournalingIncludeFields()) && count($this->getJournalingIncludeFields()) > 0;
        $use_exclude_fields = is_array($this->getJournalingExcludeFields()) && count($this->getJournalingExcludeFields()) > 0;

        $is_data_changed = false;

        $data_to_archive = array();
        foreach ($row as $key => $value) {
            if ($key == $this->getIdFieldName()) {
                continue;
            }

            // Not included or no include fields defined
            if ($use_include_fields && !in_array($key, $this->getJournalingIncludeFields())) {
                continue;
            }

            // To be excluded
            if ($use_exclude_fields && in_array($key, $this->getJournalingExcludeFields())) {
                continue;
            }

            if (isset($data[$key]) && $value != $data[$key]) {
                $is_data_changed = true;
            }

            $data_to_archive[$key] = $value;
        }

        if (!$is_data_changed) {
            return false;
        }

        $metadata = array();
        if (!$this->getJournalingSerializationMethod()) {
            $data_to_archive_serialized = json_encode($data_to_archive);
            $metadata_serialized = json_encode($metadata);
        } else {
            // TODO implement serialization
        }

        return $this->db->set('revision_datetime', date('Y-m-d H:i:s'))
            ->set('tag', $this->getJournalingTag())
            ->set('ref_id', $id)
            ->set('data_serialized', $data_to_archive_serialized)
            ->set('metadata_serialized', $metadata_serialized)
            ->insert($this->getJournalingTable());
    }

    /**
     * Returns object at a specified revision
     *
     * @param int $id
     * @param bool $revision_id
     * @return object
     * @local
     */
    public function journalingGetById($id, $revision_id = false)
    {
        $row = $this->getById($id);
        if ($revision_id && $this->getJournalingIsEnabled()) {
            $row_archived = $this->db->select('data_serialized')
                ->where('tag', $this->getJournalingTag())
                ->where('ref_id', $id)
                ->where('id', $revision_id)
                ->get($this->getJournalingTable())
                ->row();

            $data_archived = array();
            if (!$this->getJournalingUnserializationMethod()) {
                $data_archived = json_decode($row_archived->data_serialized);
            } else {
                // TODO implement deserialization
            }
            unset($row_archived->data_serialized); // Optimisation

            $data_archived = (array)$data_archived;

            // Overwriting specific fields
            foreach ($data_archived as $key => $value) {
                $row->$key = $value;
            }
            unset($data_archived);
        }

        return $row;
    }

    /**
     * Returns journaling summary for a given ID
     *
     * @param $id
     * @return mixed
     * @local
     */
    public function journalingGetRevisionSummary($id)
    {
        $result = $this->db->select('id, revision_datetime, metadata_serialized, data_serialized')
            ->where('tag', $this->getJournalingTag())
            ->where('ref_id', $id)
            ->order_by('revision_datetime', 'DESC')
            ->get($this->getJournalingTable())
            ->result();

        foreach ($result as &$row) {
            if (!$this->getJournalingUnserializationMethod()) {
                $row->metadata = json_decode($row->metadata_serialized);
            } else {
                // TODO implement deserialization
            }
        }

        return $result;
    }

    /**
     * Sets the fields that should be excluded upon journaling serialization
     *
     * @param array $journaling_exclude_fields
     * @local
     */
    public function setJournalingExcludeFields($journaling_exclude_fields)
    {
        $this->journaling_exclude_fields = $journaling_exclude_fields;
    }

    /**
     * Returns the fields that should be excluded upon journaling serialization
     *
     * @return array
     * @local
     */
    public function getJournalingExcludeFields()
    {
        return $this->journaling_exclude_fields;
    }

    /**
     * Sets the fields that should be inlucded upon journaling serialization
     *
     * @param array $journaling_include_fields
     * @local
     */
    public function setJournalingIncludeFields($journaling_include_fields)
    {
        $this->journaling_include_fields = $journaling_include_fields;
    }

    /**
     * Returns the fields that should be inluded upon journaling serialization
     *
     * @return array
     * @local
     */
    public function getJournalingIncludeFields()
    {
        return $this->journaling_include_fields;
    }

    /**
     * Enables or disables journaling
     *
     * @param bool $journaling_is_enabled
     * @local
     */
    public function setJournalingIsEnabled($journaling_is_enabled)
    {
        $this->journaling_is_enabled = $journaling_is_enabled;
    }

    /**
     * Tells whether journaling is enabled
     *
     * @return bool
     * @local
     */
    public function getJournalingIsEnabled()
    {
        return $this->journaling_is_enabled && $this->db->table_exists($this->getJournalingTable());
    }

    /**
     * Sets journaling serialization method
     *
     * @param callable|null $journaling_serialization_method
     * @local
     */
    public function setJournalingSerializationMethod($journaling_serialization_method)
    {
        // TODO pass callback instead of a method, rename
        $this->journaling_serialization_method = $journaling_serialization_method;
    }

    /**
     * Returns journaling serialization method
     *
     * @return callable|null
     * @local
     */
    public function getJournalingSerializationMethod()
    {
        // TODO pass callback instead of a method, rename
        return $this->journaling_serialization_method;
    }

    /**
     * Sets table name where serialized journaling data is saved
     *
     * @param null|string $journaling_table
     * @local
     */
    public function setJournalingTable($journaling_table)
    {
        $this->journaling_table = $journaling_table;
    }

    /**
     * Returns table name where serialized journaling data is saved
     *
     * @return null|string
     * @local
     */
    public function getJournalingTable()
    {
        if ($this->journaling_table) {
            return $this->journaling_table;
        }

        return $this->config->item('database_table_journal');
    }

    /**
     * Sets journaling tag (category identifier)
     *
     * @param null|string $journaling_tag
     * @local
     */
    public function setJournalingTag($journaling_tag)
    {
        $this->journaling_tag = $journaling_tag;
    }

    /**
     * Returns journaling tag (category identifier)
     *
     * @return null|string
     * @local
     */
    public function getJournalingTag()
    {
        return $this->journaling_tag;
    }

    /**
     * Sets journaling unserialization method
     *
     * @param callable|null $journaling_unserialization_method
     * @local
     */
    public function setJournalingUnserializationMethod($journaling_unserialization_method)
    {
        $this->journaling_unserialization_method = $journaling_unserialization_method;
    }

    /**
     * Returns journaling unserialization method
     *
     * @return callable|null
     * @local
     */
    public function getJournalingUnserializationMethod()
    {
        return $this->journaling_unserialization_method;
    }
}
