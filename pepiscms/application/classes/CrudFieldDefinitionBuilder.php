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

/**
 * Class CrudFieldDefinitionBuilder
 *
 * @since 1.0.0
 */
class CrudFieldDefinitionBuilder
{
    const LABEL_KEY = 'label';
    const DESCRIPTION_KEY = 'description';
    const INPUT_TYPE_KEY = 'input_type';
    const SHOW_IN_FORM_KEY = 'show_in_form';
    const SHOW_IN_GRID_KEY = 'show_in_grid';
    const FOREIGN_KEY_TABLE_KEY = 'foreign_key_table';
    const FOREIGN_KEY_FIELD_KEY = 'foreign_key_field';
    const FOREIGN_KEY_LABEL_FIELD_KEY = 'foreign_key_label_field';
    const FOREIGN_KEY_ACCEPT_NULL_KEY = 'foreign_key_accept_null';
    const FOREIGN_KEY_WHERE_CONDITIONS_KEY = 'foreign_key_where_conditions';
    const FOREIGN_KEY_RELATIONSHIP_TYPE_KEY = 'foreign_key_relationship_type';
    const FOREIGN_KEY_JUNCTION_ID_FIELD_LEFT_KEY = 'foreign_key_junction_id_field_left';
    const FOREIGN_KEY_JUNCTION_ID_FIELD_RIGHT_KEY = 'foreign_key_junction_id_field_right';
    const FOREIGN_KEY_JUNCTION_TABLE_KEY = 'foreign_key_junction_table';
    const VALUES_KEY = 'values';
    const VALIDATION_RULES_KEY = 'validation_rules';
    const INPUT_IS_EDITABLE_KEY = 'input_is_editable';
    const INPUT_GROUP_KEY = 'input_group';
    const INPUT_CSS_CLASS_KEY = 'input_css_class';
    const OPTIONS_KEY = 'options';
    const UPLOAD_COMPLETE_CALLBACK_KEY = 'upload_complete_callback';
    const UPLOAD_PATH_KEY = 'upload_path';
    const UPLOAD_DISPLAY_PATH_KEY = 'upload_display_path';
    const UPLOAD_ALLOWED_TYPES_KEY = 'upload_allowed_types';
    const UPLOAD_ENCRYPT_NAME_KEY = 'upload_encrypt_name';
    const GRID_FORMATING_CALLBACK_KEY = 'grid_formating_callback';
    const GRID_IS_ORDERABLE_KEY = 'grid_is_orderable';
    const GRID_CSS_CLASS_KEY = 'grid_css_class';
    const FILTER_TYPE_KEY = 'filter_type';
    const INPUT_DEFAULT_VALUE_KEY = 'input_default_value';
    const FILTER_VALUES_KEY = 'filter_values';
    const FILTER_CONDITION_KEY = 'filter_condition';
    const AUTOCOMPLETE_SOURCE_KEY = 'autocomplete_source';

    /**
     * @var CrudDefinitionBuilder
     */
    private $crudDefinitionBuilder;

    /**
     * @var array
     */
    private $definition = array();

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var PEPISCMS_Lang
     */
    private $lang;

    /**
     * @var bool
     */
    private $isWithImplicitTranslations = false;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * CrudFieldDefinitionBuilder constructor.
     *
     * @param $fieldName string
     * @param $crudDefinitionBuilder CrudDefinitionBuilder
     */
    public function __construct($fieldName, CrudDefinitionBuilder $crudDefinitionBuilder)
    {
        $this->fieldName = $fieldName;
        $this->crudDefinitionBuilder = $crudDefinitionBuilder;
    }

    /**
     * Specifies field label, this should be an already translated value.
     *
     * @param $label string
     * @return $this
     */
    public function withLabel($label)
    {
        $this->definition[self::LABEL_KEY] = $label;
        return $this;
    }

    /**
     * Specifies field description, this should be an already translated value.
     *
     * @param $description string
     * @return $this
     */
    public function withDescription($description)
    {
        $this->definition[self::DESCRIPTION_KEY] = $description;
        return $this;
    }

    /**
     * @param $inputType int
     * @return $this
     */
    public function withInputType($inputType)
    {
        $this->definition[self::INPUT_TYPE_KEY] = $inputType;
        return $this;
    }

    /**
     * Specifies whether the field is rendered in form.
     *
     * @param $showInForm bool
     * @return $this
     */
    public function withShowInForm($showInForm)
    {
        $this->definition[self::SHOW_IN_FORM_KEY] = $showInForm;
        return $this;
    }

    /**
     * Specifies whether the field is rendered in grid.
     *
     * @param $showInGrid bool
     * @return $this
     */
    public function withShowInGrid($showInGrid)
    {
        $this->definition[self::SHOW_IN_GRID_KEY] = $showInGrid;
        return $this;
    }

    /**
     * Specifies foreign key table name.
     *
     * @param $foreignKeyTable string
     * @return $this
     */
    public function withForeignKeyTable($foreignKeyTable)
    {
        $this->definition[self::FOREIGN_KEY_TABLE_KEY] = $foreignKeyTable;
        return $this;
    }

    /**
     * Specifies foreign key id field name.
     *
     * @param $foreignKeyIdField string
     * @return $this
     */
    public function withForeignKeyIdField($foreignKeyIdField)
    {
        $this->definition[self::FOREIGN_KEY_FIELD_KEY] = $foreignKeyIdField;
        return $this;
    }

    /**
     *  Specifies foreign key label field name.
     *
     * @param $foreignKeyLabelField string
     * @return $this
     */
    public function withForeignKeyLabelField($foreignKeyLabelField)
    {
        $this->definition[self::FOREIGN_KEY_LABEL_FIELD_KEY] = $foreignKeyLabelField;
        return $this;
    }

    /**
     * Specifies whether foreign key accepts null value (no relationship).
     *
     * @param $foreignKeyAcceptNull bool
     * @return $this
     */
    public function withForeignKeyAcceptNull($foreignKeyAcceptNull)
    {
        $this->definition[self::FOREIGN_KEY_ACCEPT_NULL_KEY] = $foreignKeyAcceptNull;
        return $this;
    }

    /**
     * Specifies foreign key where conditions.
     *
     * <code>
     * $foreignKeyWhereConditions = array(
     *      'database_field_name' => 'constraint_value'
     * );
     * </code>
     *
     * @param $foreignKeyWhereConditions array[string]string
     * @return $this
     */
    public function withForeignKeyWhereConditions($foreignKeyWhereConditions)
    {
        $this->definition[self::FOREIGN_KEY_WHERE_CONDITIONS_KEY] = $foreignKeyWhereConditions;
        return $this;
    }

    /**
     * Specifies database relationship type for foreign key fields.
     *
     * Allowed values are:
     * - FormBuilder::FOREIGN_KEY_ONE_TO_MANY
     * - FormBuilder::FOREIGN_KEY_MANY_TO_MANY
     *
     * @param $relationshipType
     * @return $this
     */
    public function withForeignKeyRelationshipType($relationshipType)
    {
        $this->definition[self::FOREIGN_KEY_RELATIONSHIP_TYPE_KEY] = $relationshipType;
        return $this;
    }


    /**
     * Specifies ManyToMany left id field name.
     *
     * @param $foreignKeyJunctionIdFieldLeft
     * @return $this
     */
    public function withForeignKeyJunctionIdFieldLeft($foreignKeyJunctionIdFieldLeft)
    {
        $this->definition[self::FOREIGN_KEY_JUNCTION_ID_FIELD_LEFT_KEY] = $foreignKeyJunctionIdFieldLeft;
        return $this;
    }

    /**
     * Specifies ManyToMany right id field name.
     *
     * @param $foreignKeyJunctionIdFieldRight
     * @return $this
     */
    public function withForeignKeyJunctionIdFieldRight($foreignKeyJunctionIdFieldRight)
    {
        $this->definition[self::FOREIGN_KEY_JUNCTION_ID_FIELD_RIGHT_KEY] = $foreignKeyJunctionIdFieldRight;
        return $this;
    }


    /**
     * Specifies ManyToMany junction table.
     *
     * @param $foreignKeyJunctionTable
     * @return $this
     */
    public function withForeignKeyJunctionTable($foreignKeyJunctionTable)
    {
        $this->definition[self::FOREIGN_KEY_JUNCTION_TABLE_KEY] = $foreignKeyJunctionTable;
        return $this;
    }

    /**
     * Specifies value mappings used to map grid values and select input.
     * In case there is a database entry that is not reflected in the mapping, the raw database value will be used.
     *
     * <code>
     * $values = array(
     *      'key' => 'value'
     * );
     * </code>
     *
     * @param $values array array[int|string]string
     * @return $this
     */
    public function withValues($values)
    {
        $this->definition[self::VALUES_KEY] = $values;
        return $this;
    }

    /**
     * Specifies validation rules expressed as pipe separated string.
     * The validation rule must be expressed CodeIgniter syntax or as a plain array.
     *
     * @param $validationRules string|array
     * @return $this
     */
    public function withValidationRules($validationRules)
    {
        if (is_array($validationRules)) {
            $validationRules = implode('|', $validationRules);
        }
        $this->definition[self::VALIDATION_RULES_KEY] = $validationRules;
        return $this;
    }

    /**
     * Removes all validation rules.
     *
     * @return $this
     */
    public function withNoValidationRules()
    {
        $this->definition[self::VALIDATION_RULES_KEY] = '';
        return $this;
    }

    /**
     * Adds a single validation rule. The validation rule must be expressed CodeIgniter syntax.
     *
     * @param $validationRule
     * @return $this
     */
    public function addValidationRule($validationRule)
    {
        if (isset($this->definition[self::VALIDATION_RULES_KEY])) {
            $this->definition[self::VALIDATION_RULES_KEY] .= '|' . $validationRule;
        } else {
            $this->definition[self::VALIDATION_RULES_KEY] = $validationRule;
        }

        return $this;
    }

    /**
     * Specifies whether the input is editable in form.
     *
     * @param $inputIsEditable bool
     * @return $this
     */
    public function withInputIsEditable($inputIsEditable)
    {
        $this->definition[self::INPUT_IS_EDITABLE_KEY] = $inputIsEditable;
        return $this;
    }

    /**
     * Specifies input group. This should be a translation key, it will also be used in HTML markup.
     *
     * @param $inputGroup string
     * @return $this
     */
    public function withInputGroup($inputGroup)
    {
        $this->definition[self::INPUT_GROUP_KEY] = $inputGroup;
        return $this;
    }

    /**
     * Specifies css class that will be assigned to the input element.
     *
     * @param $inputCssClass string
     * @return $this
     */
    public function withInputCssClass($inputCssClass)
    {
        $this->definition[self::INPUT_CSS_CLASS_KEY] = $inputCssClass;
        return $this;
    }

    /**
     * Specifies additional field options.
     *
     * <code>
     * $options = array(
     *      'key' => 'value'
     * );
     * </code>
     *
     * @param $options array[string]string
     * @return $this
     */
    public function withCustomOptions($options)
    {
        $this->definition[self::OPTIONS_KEY] = $options;
        return $this;
    }

    /**
     * A callback that will be executed upon successful file/image upload.
     *
     * <code>
     * function(&$filename, &$basePath, &formData, $fieldName) {
     *      $filename = 'newname';
     *      return TRUE;
     * }
     * </code>
     *
     * @param $uploadCompleteCallback callable
     * @return $this
     */
    public function withUploadCompleteCallback($uploadCompleteCallback)
    {
        $this->definition[self::UPLOAD_COMPLETE_CALLBACK_KEY] = $uploadCompleteCallback;
        return $this;
    }

    /**
     * Specifies upload (server) path.
     *
     * @param $uploadPath string
     * @return $this
     */
    public function withUploadPath($uploadPath)
    {
        $this->definition[self::UPLOAD_PATH_KEY] = $uploadPath;
        return $this;
    }

    /**
     * Specifies path prefix displayed in HTML.
     *
     * @param $uploadDisplayPath
     * @return $this
     */
    public function withUploadDisplayPath($uploadDisplayPath)
    {
        $this->definition[self::UPLOAD_DISPLAY_PATH_KEY] = $uploadDisplayPath;
        return $this;
    }

    /**
     * Specifies allowed upload files (extensions).
     *
     * Can either be expressed in CodeIgniter format or as a plain array.
     *
     * @param $uploadAllowedTypes string|array
     * @return $this
     */
    public function withUploadAllowedTypes($uploadAllowedTypes)
    {
        if (is_array($uploadAllowedTypes)) {
            $uploadAllowedTypes = implode('|', $uploadAllowedTypes);
        }
        $this->definition[self::UPLOAD_ALLOWED_TYPES_KEY] = $uploadAllowedTypes;
        return $this;
    }

    /**
     * Specifies whether the upload file name should be encrypted (randomized).
     *
     * @param $uploadEncryptName bool
     * @return $this
     */
    public function withUploadEncryptName($uploadEncryptName)
    {
        $this->definition[self::UPLOAD_ENCRYPT_NAME_KEY] = $uploadEncryptName;
        return $this;
    }

    /**
     * Specifies grid value formatting callback.
     *
     * <code>
     * function($content, $lineObject) {
     *      if($lineObject->is_important) {
     *          return '<span class="important">' . $content . '</span>';
     *      }
     *      return $content;
     * }
     * @param $gridFormattingCallback callable
     * @return $this
     */
    public function withGridFormattingCallback($gridFormattingCallback)
    {
        $this->definition[self::GRID_FORMATING_CALLBACK_KEY] = $gridFormattingCallback;
        return $this;
    }

    /**
     * Specifies whether the grid column can be used for ordering the result set.
     *
     * @param $gridIsOrderable bool
     * @return $this
     */
    public function withGridIsOrderable($gridIsOrderable)
    {
        $this->definition[self::GRID_IS_ORDERABLE_KEY] = $gridIsOrderable;
        return $this;
    }

    /**
     * Specifies grid CSS class that will be added to the resulting HTML.
     *
     * @param $gridCssClass string
     * @return $this
     */
    public function withGridCssClass($gridCssClass)
    {
        $this->definition[self::GRID_CSS_CLASS_KEY] = $gridCssClass;
        return $this;
    }

    /**
     * Specifies filter type.
     *
     * Can be:
     * - DataGrid::FILTER_BASIC
     * - DataGrid::FILTER_SELECT
     * - DataGrid::FILTER_DATE
     * - DataGrid::FILTER_MULTIPLE_SELECT
     * - DataGrid::FILTER_MULTIPLE_CHECKBOX
     * - DataGrid::FILTER_FORCED
     *
     * @param $filterType int
     * @return $this
     */
    public function withFilterType($filterType)
    {
        $this->definition[self::FILTER_TYPE_KEY] = $filterType;
        return $this;
    }

    /**
     * Specifies input default value.
     *
     * @param $inputDefaultValue string
     * @return $this
     */
    public function withInputDefaultValue($inputDefaultValue)
    {
        $this->definition[self::INPUT_DEFAULT_VALUE_KEY] = $inputDefaultValue;
        return $this;
    }

    /**
     * Specifies filter value mapping.
     *
     * <code>
     * $filterValues = array(
     *      'key' => 'value'
     * );
     * </code>
     *
     * @param $filterValues array[int|string]string
     * @return $this
     */
    public function withFilterValues($filterValues)
    {
        $this->definition[self::FILTER_VALUES_KEY] = $filterValues;
        return $this;
    }

    /**
     * Specifies filter condition.
     *
     * Filter condition must be one of the values:
     * - DataGrid::FILTER_CONDITION_EQUAL
     * - DataGrid::FILTER_CONDITION_NOT_EQUAL
     * - DataGrid::FILTER_CONDITION_GREATER
     * - DataGrid::FILTER_CONDITION_GREATER_OR_EQUAL
     * - DataGrid::FILTER_CONDITION_LESS
     * - DataGrid::FILTER_CONDITION_LESS_OR_EQUAL
     * - DataGrid::FILTER_CONDITION_LIKE
     * - DataGrid::FILTER_CONDITION_IN
     *
     * @param $filterCondition
     * @return $this
     */
    public function withFilterCondition($filterCondition)
    {
        $this->definition[self::FILTER_CONDITION_KEY] = $filterCondition;
        return $this;
    }

    /**
     * Specifies path to autocomplete source.
     *
     * @param $autocompleteSource string
     * @return $this
     */
    public function withAutocompleteSource($autocompleteSource)
    {
        $this->definition[self::AUTOCOMPLETE_SOURCE_KEY] = $autocompleteSource;
        return $this;
    }

    /**
     * @return CrudDefinitionBuilder
     */
    public function end()
    {
        return $this->crudDefinitionBuilder;
    }

    /**
     * Enables implicit translations
     *
     * @param $moduleName string
     * @param $lang PEPISCMS_Lang
     * @return $this
     */
    public function withImplicitTranslations($moduleName, $lang)
    {
        $this->moduleName = $moduleName;
        $this->lang = $lang;
        $this->isWithImplicitTranslations = true;
        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        $definition = $this->definition;
        $definition[self::LABEL_KEY] = $this->getTranslatedValue($definition, self::LABEL_KEY);
        $definition[self::DESCRIPTION_KEY] = $this->getTranslatedValue($definition, self::DESCRIPTION_KEY, '_description');
        // Setting default input group
        if (!isset($definition[self::INPUT_GROUP_KEY]) || !$definition[self::INPUT_GROUP_KEY]) {
            $definition[self::INPUT_GROUP_KEY] = 'default';
        }

        return $definition;
    }

    /**
     * @param array $definition
     * @param $fieldKey
     * @param $suffix
     * @return array
     */
    private function getTranslatedValue(array $definition, $fieldKey, $suffix = '')
    {

        $translatedValue = false;
        if ($this->lang !== null) {
            if (!isset($definition[$fieldKey])) {
                $translatedValue = $this->lang->line($this->moduleName . '_' . $this->fieldName . $suffix, false);
            } else {
                if (strpos($definition[$fieldKey], ' ') === false) {
                    $translatedValue = $this->lang->line($definition[$fieldKey], false);
                }
            }
        }


        if ($translatedValue) {
            return $translatedValue;
        }

        return isset($definition[$fieldKey]) ? $definition[$fieldKey] : false;
    }
}
