<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See LICENSE.txt
 * @link                http://www.polak.ro/
 */

/**
 * Table utility
 */
class TableUtility
{
    private $db;
    private $foreign_keys_cache = null;
    private $raw_table_description_cache = null;

    /**
     * Default constructor
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
        if (isset($params['database_group']) && $params['database_group']) {
            require INSTALLATIONPATH . 'application/config/database.php';
            if (!isset($db[$params['database_group']])) {
                return FALSE;
            }
            /** @noinspection PhpUndefinedVariableInspection */
            $this->db = get_instance()->load->database($db[$params['database_group']], TRUE);
        } else {
            $this->db = get_instance()->db;
        }

        CI_Controller::get_instance()->load->library('DataGrid');
        CI_Controller::get_instance()->load->library('FormBuilder');

        $this->raw_table_description_cache = array();
    }

    /**
     * Returns all tables definition
     *
     * @return array
     */
    function getTablesDefinition()
    {
        $query = $this->db->query('SHOW TABLES');
        $tables_a = $query->result_array();
        $tables = array();

        foreach ($tables_a as $table) {
            $table = array_pop($table);
            $tables[$table] = $this->getDefinitionFromTable($table);
        }

        return $tables;
    }

    /**
     * Returns all foreign keys found in the database
     *
     * @return array
     */
    public function getForeignKeys()
    {
        if ($this->foreign_keys_cache === NULL) {
            $query = $this->db->query('SELECT table_name, column_name, referenced_table_name, referenced_column_name from information_schema.key_column_usage where referenced_table_name is not null');
            $foreign_keys_a = $query->result_array();
            $this->foreign_keys_cache = array();

            foreach ($foreign_keys_a as $key) {
                $this->foreign_keys_cache[$key['table_name']][$key['column_name']] = array($key['referenced_table_name'], $key['referenced_column_name']);
            }
        }

        return $this->foreign_keys_cache;
    }

    /**
     * Tells whether the table exists
     *
     * @param String $table
     * @return Boolean
     */
    public function tableExists($table)
    {
        return $this->db->table_exists($table);
    }

    /**
     * @param $table
     * @return array
     */
    public function getForeignKeysRelatedToTable($table)
    {
        $related_foreign_keys = array();

        $fks = $this->getForeignKeys();
        foreach ($fks as $_table => $column_fk) {
            foreach ($column_fk as $_column => $fk) {
                if ($fk[0] == $table) {
                    $related_foreign_keys[$_table][$_column] = $fk;
                }
            }
        }

        return $related_foreign_keys;
    }

    /**
     * @param $table
     * @return array
     */
    public function getManyToManyRelationshipsRelatedToTable($table)
    {
        $related_foreign_keys = $this->getForeignKeysRelatedToTable($table);

        $many_to_many_fks = array();

        foreach ($related_foreign_keys as $_table => $column_fk) {
            // Take table and check its fields, if all of them are FK then take it
            $table_raw_description = $this->getTableRawDescription($_table);

            foreach ($table_raw_description as $column) {
                $key = strtoupper($column['Key']);

                // We need tables that contain primary and FK keys only
                if ($key != 'PRI' && $key != 'MUL') {
                    unset($related_foreign_keys[$_table]);
                    break;
                }
            }
        }

        $foreign_keys = $this->getForeignKeys();


        foreach ($related_foreign_keys as $_table => $column_fk) {
            foreach ($column_fk as $_column => $fk) {
                $my_gfk_column = FALSE;
                foreach ($foreign_keys[$_table] as $gfk_column => $fk) {
                    if ($gfk_column != $_column) {
                        $my_gfk_column = $gfk_column;
                        $my_fk = $fk; // Posible misinterpretation - only one key is taken
                        break;
                    }
                }

                $many_to_many_fks[] = array(
                    'foreign_key_table' => $my_fk[0],
                    'foreign_key_field' => $my_fk[1],
                    'foreign_key_label_field' => $this->getTableLabelFieldName($my_fk[0], array($my_fk[1])),
                    'foreign_key_junction_id_field_left' => $_column,
                    'foreign_key_junction_id_field_right' => $my_gfk_column,
                    'foreign_key_junction_table' => $_table,
                );
            }
        }

        return $many_to_many_fks;
    }

    /**
     * Returns RAW table description (DB dependent)
     *
     * @param $table
     * @return bool|array
     */
    private function getTableRawDescription($table)
    {
        if (isset($this->raw_table_description_cache[$table])) {
            return $this->raw_table_description_cache[$table];
        }
        $query = $this->db->query('DESCRIBE ' . $table);
        if (!$query) {
            return FALSE;
        }
        $this->raw_table_description_cache[$table] = $query->result_array();

        return $this->raw_table_description_cache[$table];
    }

    /**
     * Return field name that fits for the label of the table
     *
     * @param $table
     * @param array $exclude_field_names
     * @return bool|string
     */
    public function getTableLabelFieldName($table, $exclude_field_names = array())
    {
        $foreign_key_label_field = 'id';

        // Try from set of possible labels
        $allowed_labels = array('name', 'label', 'username', 'login', 'fitst_name', 'last_name', 'code', 'id');
        $fk_table_definition = $this->getDefinitionFromTable($table, FALSE);


        foreach ($allowed_labels as $allowed_label) {
            if (isset($fk_table_definition[$allowed_label])) {
                return $allowed_label;
                break;
            }
        }

        // Not found in the first trial
        // Find element that is not ID,
        foreach ($fk_table_definition as $key => $value) {
            if (in_array($key, $exclude_field_names)) {
                continue;
            }

            // Must contain name OR not numeric and required
            if (strpos($key, 'name') !== FALSE
                || (strpos($value['validation_rules'], 'required') !== FALSE
                    && strpos($value['validation_rules'], 'numeric') === FALSE
                    && $value['input_type'] == FormBuilder::TEXTFIELD)) {
                return $key;
            }
        }
    }

    /**
     * Returns CRUD ready table definition
     *
     * @param String $table
     * @param Boolean $resolve_fk
     * @return array
     */
    public function getDefinitionFromTable($table, $resolve_fk = true)
    {
        $collumns_a = $this->getTableRawDescription($table);
        if (!$collumns_a) {
            return FALSE;
        }

        // Reading all foreign keys
        $foreign_keys = array();
        $many_to_many_fks = array();
        if ($resolve_fk) {
            $foreign_keys = $this->getForeignKeys();
            $many_to_many_fks = $this->getManyToManyRelationshipsRelatedToTable($table);
        }


        // Output definition
        $definition = array();

        // Taking raw collums and transforming them into definition
        foreach ($collumns_a as $collumn) {
            $show_in_grid = $show_in_form = TRUE;
            $field_name = $collumn['Field'];
            $is_null = strtolower($collumn['Null']) == 'yes';
            $validation_rules = array();
            $db_type = self::resolveDBType($collumn['Type']);
            $max_input_length = self::resolveLengthFronDBType($collumn['Type']);
            $input_type = self::getInputType(self::resolveDBType($collumn['Type']), $field_name);
            $is_numeric = self::isDbTypeNumeric($db_type);
            $is_boolean = $is_numeric && ($db_type == 'smallint' || $db_type == 'tinyint');
            $is_unique = strpos(strtoupper($collumn['Key']), 'UNI') !== FALSE;
            $is_primary_key = strpos(strtoupper($collumn['Key']), 'PRI') !== FALSE;

            $definition[$field_name] = array();
            if ($collumn['Default']) {
                $definition[$field_name]['input_default_value'] = $collumn['Default'];
            }

//            if ($is_unique)
//            {
//                $validation_rules[] = 'is_unique[' . $table . '.' . $field . ']';
//            }

            if (!$is_null && !$is_boolean) {
                $validation_rules[] = 'required';
            }

            if (strpos(strtolower($field_name), 'email') !== FALSE) {
                $validation_rules[] = 'valid_email';
            }

            if ($is_numeric) {
                // For numeric fields
                $validation_rules[] = 'numeric';
                if ($is_boolean) {
                    $input_type = FormBuilder::CHECKBOX;
                    $definition[$field_name]['filter_type'] = DataGrid::FILTER_SELECT;
                }
            } else {
                // For text fields
                if ($max_input_length) {
                    $validation_rules[] = 'max_length[' . $max_input_length . ']';
                }

                if (strpos($field_name, 'color') !== FALSE || strpos($field_name, 'colour') !== FALSE) {
                    $input_type = FormBuilder::COLORPICKER;
                } elseif (strpos($field_name, 'path') !== FALSE || strpos($field_name, 'image') !== FALSE || strpos($field_name, 'file') !== FALSE || strpos($field_name, 'icon') !== FALSE || strpos($field_name, 'logo') !== FALSE || strpos($field_name, 'baner') !== FALSE || strpos($field_name, 'thumb') !== FALSE || strpos($field_name, 'img') !== FALSE) {
                    $input_type = FormBuilder::FILE;
                    $definition[$field_name]['upload_path'] = 'uploads/';
                    $definition[$field_name]['upload_display_path'] = 'uploads/';

                    if (strpos($field_name, 'image') !== FALSE || strpos($field_name, 'icon') !== FALSE || strpos($field_name, 'logo') !== FALSE || strpos($field_name, 'baner') !== FALSE || strpos($field_name, 'thumb') !== FALSE) {
                        $input_type = FormBuilder::IMAGE;
                    } else {
                        $definition[$field_name]['upload_allowed_types'] = '*';
                    }
                }

                if ($input_type == FormBuilder::DATE || $input_type == FormBuilder::TIMESTAMP) {
                    $definition[$field_name]['filter_type'] = DataGrid::FILTER_DATE;
                } elseif ($input_type == FormBuilder::IMAGE || $input_type == FormBuilder::FILE || $input_type == FormBuilder::COLORPICKER) {
                    unset($definition[$field_name]['filter_type']);
                } else {
                    $definition[$field_name]['filter_type'] = DataGrid::FILTER_BASIC;
                }
            }


            // Prevent loops
            if ($resolve_fk) {
                $foreign_key = (isset($foreign_keys[$table][$collumn['Field']]) ? $foreign_keys[$table][$collumn['Field']] : FALSE);
                if ($foreign_key) {
                    $foreign_key_label_field = $this->getTableLabelFieldName($foreign_key[0], array($foreign_key[1]));

                    // Default, most probably ID
                    if (!$foreign_key_label_field) {
                        $foreign_key_label_field = $foreign_key[1];
                    }

                    $input_type = FormBuilder::SELECTBOX;
                    $definition[$field_name]['foreign_key_relationship_type'] = FormBuilder::FOREIGN_KEY_ONE_TO_MANY;
                    $definition[$field_name]['foreign_key_table'] = $foreign_key[0];
                    $definition[$field_name]['foreign_key_field'] = $foreign_key[1];
                    $definition[$field_name]['foreign_key_label_field'] = $foreign_key_label_field;
                    $definition[$field_name]['filter_type'] = DataGrid::FILTER_SELECT;
                    $definition[$field_name]['foreign_key_accept_null'] = TRUE;
                }
            }

            // Transforming longer textfields into textareas
            if ($input_type == FormBuilder::TEXTFIELD) {
                if ($max_input_length > 255) {
                    $input_type = FormBuilder::TEXTAREA;
                }
            }

            // We dont want to display longer fields and paths in grid view
            if ($input_type == FormBuilder::RTF || $input_type == FormBuilder::FILE || $input_type == FormBuilder::IMAGE) {
                $show_in_grid = FALSE;
            }


            $definition[$field_name]['show_in_grid'] = $show_in_grid;
            $definition[$field_name]['show_in_form'] = $show_in_form;
            $definition[$field_name]['input_type'] = $input_type;
            $definition[$field_name]['validation_rules'] = implode('|', $validation_rules);
        }

        foreach ($many_to_many_fks as $many_to_many_fk) {
            $field_name = $many_to_many_fk['foreign_key_junction_table'];
            $definition[$field_name] = $many_to_many_fk;
            $definition[$field_name]['foreign_key_relationship_type'] = FormBuilder::FOREIGN_KEY_MANY_TO_MANY;
            $definition[$field_name]['show_in_grid'] = TRUE;
            $definition[$field_name]['show_in_form'] = TRUE;
            $definition[$field_name]['input_type'] = FormBuilder::MULTIPLECHECKBOX;
            $definition[$field_name]['validation_rules'] = '';
            $definition[$field_name]['filter_type'] = DataGrid::FILTER_SELECT;
        }

        return $definition;
    }

    /**
     * Checks whether db time is of numeric type
     *
     * @param string $db_type
     * @return boolean
     */
    public static function isDbTypeNumeric($db_type)
    {
        if ($db_type == 'int' || $db_type == 'double' || $db_type == 'smallint' || $db_type == 'tinyint') {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Returns input type based on database type
     *
     * @param string $type
     * @param string $field_name
     * @return Integer
     */
    public static function getInputType($type, $field_name)
    {
        if ($type == 'smallint') {
            return FormBuilder::CHECKBOX;
        } elseif (self::isDbTypeNumeric($type)) {
            return FormBuilder::TEXTFIELD;
        } elseif ($type == 'longtext' || $type == 'text' || $type == 'mediumtext') {
            return FormBuilder::RTF;
        } elseif ($type == 'date') {
            return FormBuilder::DATE;
        } elseif ($type == 'datetime' || $type == 'timestamp') {
            return FormBuilder::TIMESTAMP;
        }

        if (strpos($field_name, 'passw') !== FALSE) {
            return FormBuilder::PASSWORD;
        }

        return FormBuilder::TEXTFIELD;
    }

    /**
     * Extracts database length from field definition
     *
     * @param string $type
     * @return bool|string
     */
    public static function resolveLengthFronDBType($type)
    {
        $pos = strpos($type, '(');
        if ($pos !== FALSE) {
            return substr($type, $pos + 1, (strpos($type, ')') - $pos) - 1);
        }

        return FALSE;
    }

    /**
     * Returns field type identificator
     *
     * @param String $type
     * @return String
     */
    public static function resolveDBType($type)
    {
        $pos = strpos($type, '(');
        if ($pos !== FALSE) {
            return substr($type, 0, $pos);
        } else {
            $pos = strpos($type, ' ');
            if ($pos !== FALSE) {
                return substr($type, 0, $pos);
            } else {
                return $type;
            }
        }
    }


    /**
     * Orders fields by predefined order
     *
     * @param $definition
     * @return mixed
     */
    public function orderFieldsByImportance($definition)
    {
        $order = array(
            FormBuilder::TEXTFIELD,
            FormBuilder::PASSWORD,
            FormBuilder::COLORPICKER,
            FormBuilder::SELECTBOX,
            FormBuilder::CHECKBOX,
            FormBuilder::MULTIPLECHECKBOX,
            FormBuilder::TEXTAREA,
            FormBuilder::IMAGE,
            FormBuilder::FILE,
            FormBuilder::DATE,
            FormBuilder::TIMESTAMP,
            FormBuilder::RTF,
            FormBuilder::RTF_FULL,
        );

        $def_ref = $definition;
        uasort($def_ref, function ($a, $b) use ($order) {
            $posA = array_search($a['input_type'], $order);
            $posB = array_search($b['input_type'], $order);

            if ($posA === FALSE) {
                return 1;
            }

            if ($posB === FALSE) {
                return -1;
            }

            return $posA - $posB;
        });

        return $def_ref;
    }

}
