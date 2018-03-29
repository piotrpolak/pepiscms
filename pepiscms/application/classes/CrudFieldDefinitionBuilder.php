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
        $this->definition['label'] = $label;
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
        $this->definition['description'] = $description;
        return $this;
    }

    /**
     * @param $inputType int
     * @return $this
     */
    public function withInputType($inputType)
    {
        $this->definition['input_type'] = $inputType;
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
        $this->definition['show_in_form'] = $showInForm;
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
        $this->definition['show_in_grid'] = $showInGrid;
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
        $this->definition['foreign_key_table'] = $foreignKeyTable;
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
        $this->definition['foreign_key_field'] = $foreignKeyIdField;
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
        $this->definition['foreign_key_label_field'] = $foreignKeyLabelField;
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
        $this->definition['foreign_key_accept_null'] = $foreignKeyAcceptNull;
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
        $this->definition['foreign_key_where_conditions'] = $foreignKeyWhereConditions;
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
        $this->definition['foreign_key_relationship_type'] = $relationshipType;
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
        $this->definition['foreign_key_junction_id_field_left'] = $foreignKeyJunctionIdFieldLeft;
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
        $this->definition['foreign_key_junction_id_field_right'] = $foreignKeyJunctionIdFieldRight;
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
        $this->definition['foreign_key_junction_table'] = $foreignKeyJunctionTable;
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
        $this->definition['values'] = $values;
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
        $this->definition['validation_rules'] = $validationRules;
        return $this;
    }

    /**
     * Removes all validation rules.
     *
     * @return $this
     */
    public function withNoValidationRules()
    {
        $this->definition['validation_rules'] = '';
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
        if (isset($this->definition['validation_rules'])) {
            $this->definition['validation_rules'] .= '|' . $validationRule;
        } else {
            $this->definition['validation_rules'] = $validationRule;
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
        $this->definition['input_is_editable'] = $inputIsEditable;
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
        $this->definition['input_group'] = $inputGroup;
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
        $this->definition['input_css_class'] = $inputCssClass;
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
        $this->definition['options'] = $options;
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
        $this->definition['upload_complete_callback'] = $uploadCompleteCallback;
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
        $this->definition['upload_path'] = $uploadPath;
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
        $this->definition['upload_display_path'] = $uploadDisplayPath;
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
        $this->definition['upload_allowed_types'] = $uploadAllowedTypes;
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
        $this->definition['upload_encrypt_name'] = $uploadEncryptName;
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
        $this->definition['grid_formating_callback'] = $gridFormattingCallback;
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
        $this->definition['grid_is_orderable'] = $gridIsOrderable;
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
        $this->definition['grid_css_class'] = $gridCssClass;
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
        $this->definition['filter_type'] = $filterType;
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
        $this->definition['input_default_value'] = $inputDefaultValue;
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
        $this->definition['filter_values'] = $filterValues;
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
        $this->definition['filter_condition'] = $filterCondition;
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
        $this->definition['autocomplete_source'] = $autocompleteSource;
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

        // Getting label
        if (!isset($definition['label'])) {
            if ($this->lang !== null) {
                $definition['label'] = $this->lang->line($this->moduleName . '_' . $this->fieldName);
            }
        }

        // Getting description
        if (!isset($definition['description'])) {
            if ($this->lang !== null) {
                $description = $this->lang->line($this->moduleName . '_' . $this->fieldName . '_description', false);
                if ($description !== false) {
                    $definition['description'] = $description;
                }
            }
        }

        // Setting default input group
        if (!isset($definition['input_group']) || !$definition['input_group']) {
            $definition['input_group'] = 'default';
        }

        return $definition;
    }
}
