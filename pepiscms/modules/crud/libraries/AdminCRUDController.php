<?php

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

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller class for building admin CRUD Controllers with no effort
 * The controller supposes every CRUD consist of index (display), edit (Create/Update), preview and delete actions.
 *
 * You can overwrite module views by creating respective files inside your module
 *
 * @since 0.2.0
 */
abstract class AdminCRUDController extends ModuleAdminController
{
    /**
     * Database table name used for Generic_model when no feed object is specified
     *
     * @var string
     */
    private $table = '';

    /**
     * Where conditions to be applied for CRUD read
     *
     * @var bool|array
     */
    private $where = false;

    /**
     * CRUD definition used for FormBuilder and DataGrid
     *
     * @var bool|array
     */
    private $definition = false;

    /**
     * CRUD template files path
     *
     * @var string
     */
    private $template_path = '';

    /**
     * Current module template files path
     *
     * @var string
     */
    private $current_module_template_path = '';

    /**
     * Name of the column used for ordering
     *
     * @var string
     */
    private $item_order_column = 'item_order';

    /**
     * Name of the column used for constraining the order - the order action is applied only among rows having the same
     * constraining column value
     *
     * @var bool|string
     */
    private $item_order_constraint_column = false;

    /**
     * Name of the column used for stating
     *
     * @var string
     */
    private $is_stared_column = 'is_stared';

    /**
     * Variable used for overwriting star action label
     *
     * @var bool|string
     */
    private $is_stared_label = false;

    /**
     * Variable indicating whether the CRUD is orderable
     *
     * @var bool
     */
    private $is_orderable = false;

    /**
     * Variable indicating whether the CRUD is starable
     *
     * @var bool
     */
    private $is_starable = false;


    /**
     * Variable indicating whether the CRUD is addable
     *
     * @var bool
     */
    private $is_addable = true;

    /**
     * Variable indicating whether the CRUD is editable
     *
     * @var bool
     */
    private $is_editable = true;

    /**
     * Variable indicating whether the CRUD is deletable
     *
     * @var bool
     */
    private $is_deletable = true;

    /**
     * Variable indicating whether the CRUD is previewable
     *
     * @var bool
     */
    private $is_previewable = true;

    /**
     * Variable indicating whether the CRUD is importable
     *
     * @var bool
     */
    private $is_importable = false;

    /**
     * Array containing list of fields that must be specified for the the entry to be imported
     *
     * @var bool|array
     */
    private $is_importable_must_have_fields = false;

    /**
     * Data filtering callback
     *
     * @var bool|callback
     */
    private $is_importable_data_formatting_callback = false;

    /**
     * Variable indicating whether the CRUD data is exportable
     *
     * @var bool
     */
    private $is_exportable = false;

    /**
     * @var bool
     */
    private $is_exportable_data_formatting_callback = false;


    /**
     * When set to false, the edit action is still accessible but not displayed in DataGrid
     *
     * @var bool
     */
    private $is_edit_action_displayed_in_grid = true;

    /**
     * Feed object used by both DataGrid and FormBuilder
     *
     * @var bool|Generic_model
     */
    private $feed_object = false;

    /**
     * Whether the popup action is enabled
     *
     * @var bool
     */
    private $is_popup_enabled = true;

    /**
     * Image field name
     *
     * @var bool|string
     */
    private $meta_image_field = false;

    /**
     * Base URL for displaying meta images
     * @var bool|string
     */
    private $meta_image_base_url = false;

    /**
     * Pattern used by meta title
     *
     * @var bool|string
     */
    private $meta_title_pattern = false;

    /**
     * Some kind of cache
     *
     * @var bool|array
     */
    private $meta_title_pattern_keys = false;

    /**
     * Pattern used by meta description
     *
     * @var bool|string
     */
    private $meta_description_pattern = false;

    /**
     * Callback used by meta description
     *
     * @var bool|callback
     */
    private $meta_description_pattern_callback = false;

    /**
     * Some kind of cache
     *
     * @var bool|array
     */
    private $meta_description_pattern_keys = false;

    /**
     * Label displayed in meta order column
     *
     * @var bool|string
     */
    private $meta_order_field_label = false;

    /**
     * Meta order field
     *
     * @var string
     */
    private $meta_order_field = 'id';

    /**
     * ID field name used for CRUD operations
     *
     * @var string
     */
    private $id_field_name = 'id';

    /**
     * Variable used for overwriting add new item label
     *
     * @var bool|string
     */
    private $add_new_item_label = false;

    /**
     * Variable used for overwriting back to items label
     *
     * @var bool|string
     */
    private $back_to_items_label = false;

    /**
     * DataGrid meta actions
     *
     * @var array
     */
    private $datagrid_meta_actions = array();

    /**
     * Parameter that is passed to DataGrid generate method
     * @var bool|mixed
     */
    private $datagrid_generate_parameter = false;

    /**
     * Tooltip text for index, false if hidden
     *
     * @var bool|string
     */
    private $tooltip_text_for_index = false;

    /**
     * Tooltip text for edit, false if hidden
     *
     * @var bool|string
     */
    private $tooltip_text_for_edit = false;

    /**
     * Array containing the list of actions to be rendered on index page
     *
     * @var array
     */
    private $actions_for_index = array();

    /**
     * Array containing the list of actions to be rendered on edit page
     *
     * @var array
     */
    private $actions_for_edit = array();

    /**
     * URL used as a back link on index page
     *
     * @var bool|string
     */
    private $back_action_for_index = false;

    /**
     * HTML Meta title
     *
     * @var string
     */
    private $title = '';

    /**
     * Array containing the list of forced filters
     *
     * @var bool|array
     */
    private $_forced_filters = false;

    /**
     * URL used as a back link on edit pages
     *
     * @var bool|string
     */
    private $back_link_for_edit = false;

    /**
     * Base module URL for index
     *
     * @var bool|string
     */
    private $base_module_url = false;

    /**
     * @deprecated as PepisCMS 0.2.4.1
     */
    private $related_module_name = false;

    /**
     * @deprecated as PepisCMS 0.2.4.1
     */
    private $related_module_filter_name = false;


    /**
     * Default constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->assign('running_module', $this->modulerunner->getRunningModuleName());

        $this->base_module_url = module_url();

        $this->load->library('DataGrid');
        $this->load->library('FormBuilder');

        $this->load->moduleLanguage('crud', 'crud');
        $this->template_path = module_path('crud') . '/views/admin/';
        $this->current_module_template_path = module_path() . '/views/admin/';

        $this->assign('bredcrumb_steps_assoc_array', '');
    }

    /**
     * Returns name of the current module
     *
     * @since 0.2.4.1
     * @return string
     */
    protected function getModuleName()
    {
        return strtolower(str_replace('Admin', '', get_class($this)));
    }

    /**
     * Returns name of the current module's model
     *
     * @since 0.2.4.1
     * @return string
     */
    protected function getModelName()
    {
        $model_name = ucfirst($this->getModuleName());
        if (substr($model_name, strlen($model_name) - 3) == 'ies') {
            $model_name = substr($model_name, 0, strlen($model_name) - 3) . 'y';
        } elseif (substr($model_name, strlen($model_name) - 2) == 'es') {
            $model_name = substr($model_name, 0, strlen($model_name) - 2);
        } elseif ($model_name{strlen($model_name) - 1} == 's') {
            $model_name = substr($model_name, 0, strlen($model_name) - 1);
        }
        $model_name .= '_model';

        return $model_name;
    }

    /**
     * Returns template path
     *
     * @return string
     */
    protected function getTemplatePath()
    {
        return $this->template_path;
    }

    /**
     * Sets explanation message
     * @param String $tooltip_text
     * @return AdminCRUDController
     */
    protected function setTooltipTextForIndex($tooltip_text)
    {
        $this->tooltip_text_for_index = $tooltip_text;
        return $this;
    }

    /**
     * Sets explanation message
     * @param string $tooltip_text
     * @return AdminCRUDController
     */
    protected function setTooltipTextForEdit($tooltip_text)
    {
        $this->tooltip_text_for_edit = $tooltip_text;
        return $this;
    }

    /**
     * Adds an action that will be displayed in index using display_action_bar function
     * Allowed keys: title, name, icon, link
     *
     * @param Array $action
     * @return AdminCRUDController
     */
    protected function addActionForIndex($action)
    {
        $this->actions_for_index[] = $action;
        return $this;
    }

    /**
     * Sets back action for index, to be used when the module is a subiew
     * Allowed keys: title, name, icon, link
     *
     * @param Array $action
     * @return AdminCRUDController
     */
    protected function setBackActionForIndex($action)
    {
        // TODO solve it with back URL (not action)
        if (!isset($action['icon'])) {
            $action['icon'] = 'pepiscms/theme/img/dialog/actions/back_16.png';
        }

        $this->back_action_for_index = $action;
        return $this;
    }

    /**
     * Adds an action that will be displayed in index using display_action_bar function
     * Allowed keys: title, name, icon, link
     *
     * @param Array $action
     * @return AdminCRUDController
     */
    protected function addActionForEdit($action)
    {
        $this->actions_for_edit[] = $action;
        return $this;
    }

    /**
     * Sets back URL for edit action
     *
     * There are situations when it is not desired to redirect the user to index
     * You can use this method to change the default behavior
     *
     * @param String $back_link_for_edit
     * @return AdminCRUDController
     */
    protected function setBackLinkForEdit($back_link_for_edit)
    {
        $this->back_link_for_edit = $back_link_for_edit;
        return $this;
    }

    /**
     * Returns back URL for edit action
     *
     * @return String
     */
    protected function getBackLinkForEdit()
    {
        if (!$this->back_link_for_edit) {
            return $this->getModuleBaseUrl() . 'index' . ($this->input->getParam('order_by') ? '/order_by-' . $this->input->getParam('order_by') : '') . ($this->input->getParam('order') ? '/order-' . $this->input->getParam('order') : '') . ($this->input->getParam('filters') ? '/filters-' . $this->input->getParam('filters') : '') . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '');
        }

        return $this->back_link_for_edit;
    }

    /**
     * Returns base module URL for links
     *
     * @return string
     */
    protected function getModuleBaseUrl()
    {
        return $this->base_module_url;
    }

    /**
     * Sets base module URL for links
     *
     * @param $base_module_url
     * @return AdminCRUDController
     */
    protected function setModuleBaseUrl($base_module_url)
    {
        $this->base_module_url = $base_module_url;
        return $this;
    }

    /**
     * Sets breadcrumbs steps, associative array URL => LABEL
     *
     * @param Array $bredcrumb_steps_assoc_array
     * @return AdminCRUDController
     */
    protected function setBreadcrumbPathSteps($bredcrumb_steps_assoc_array)
    {
        $this->assign('bredcrumb_steps_assoc_array', $bredcrumb_steps_assoc_array);
        return $this;
    }

    /**
     * Set fields definition compatible with FormBuilder and DataGrid
     *
     * @param Array $definition
     * @return AdminCRUDController
     */
    protected function setDefinition($definition)
    {
        $this->definition = $definition;
        return $this;
    }

    /**
     * Removes filter by field name
     *
     * @param String $field
     * @return AdminCRUDController
     */
    protected function removeFilterByFieldName($field)
    {
        if (isset($this->definition[$field]['filter_type'])) {
            $this->definition[$field]['filter_type'] = false;
        }
        return $this;
    }

    /**
     * Returns CRUD definition
     *
     * @return Array
     */
    protected function getDefinition()
    {
        if (!$this->definition) {
            $definition = array();
            $fields = $this->db->list_fields($this->getTable());
            foreach ($fields as $field) {
                $definition[] = array('field' => $field);
            }

            return $definition;
        }

        return $this->definition;
    }

    /**
     * When set to true, the user can change order of elements.
     * $item_order_column contains the name of collum containing order number.
     * By default $item_order_column is set to item_order.
     *
     * @param bool $is_orderable
     * @param bool|string $item_order_column
     * @param bool|string $item_order_constraint_column
     * @return $this
     */
    protected function setOrderable($is_orderable = true, $item_order_column = false, $item_order_constraint_column = false)
    {
        if ($item_order_column !== false) {
            $this->item_order_column = $item_order_column;
        }

        if ($item_order_constraint_column !== false) {
            $this->item_order_constraint_column = $item_order_constraint_column;
        }

        $this->is_orderable = $is_orderable;

        if ($this->is_orderable) {
            $this->datagrid->setDefaultOrder($this->item_order_column);
        } else {
            $this->datagrid->setDefaultOrder(false);
        }

        return $this;
    }

    /**
     * Makes the rows starable. You can use it to promote some items
     *
     * @param bool $is_starable
     * @param bool|string $is_stared_column
     * @param bool|string $is_stared_label
     * @return $this
     */
    protected function setStarable($is_starable = true, $is_stared_column = false, $is_stared_label = false)
    {
        if ($is_stared_column !== false) {
            $this->is_stared_column = $is_stared_column;
        }

        $this->is_stared_label = $is_stared_label;
        $this->is_starable = $is_starable;

        return $this;
    }

    /**
     * Tells whether the form is starable
     *
     * @return bool
     */
    protected function isStarable()
    {
        return $this->is_starable;
    }

    /**
     * Makes the CRUD importable
     *
     * @param bool $is_importable
     * @param bool $must_have_fields
     * @param bool $data_formatting_callback
     * @return $this
     */
    protected function setImportable($is_importable = true, $must_have_fields = false, $data_formatting_callback = false)
    {
        $this->is_importable = $is_importable;
        $this->is_importable_must_have_fields = $must_have_fields;
        $this->is_importable_data_formatting_callback = $data_formatting_callback;

        return $this;
    }

    /**
     * Tells whether the import action is enabled
     *
     * @return bool
     */
    protected function isImportable()
    {
        return $this->is_importable;
    }

    /**
     * Enables or disables export action
     *
     * @param bool $is_exportable
     * @param bool $data_formatting_callback
     * @return $this
     */
    protected function setExportable($is_exportable = true, $data_formatting_callback = false)
    {
        $this->is_exportable = $is_exportable;
        $this->is_exportable_data_formatting_callback = $data_formatting_callback;
        return $this;
    }

    /**
     * Tells whether export action is enabled
     *
     * @return bool
     */
    protected function isExportable()
    {
        return $this->is_exportable;
    }

    /**
     * Tells whether the form is orderable
     *
     * @return bool
     */
    protected function isOrderable()
    {
        return $this->is_orderable;
    }

    /**
     * Enables or disables edit action
     *
     * @param boolean $is_editable
     * @return AdminCRUDController
     */
    protected function setEditable($is_editable = true)
    {
        $this->is_editable = $is_editable;
        return $this;
    }

    /**
     * Tells whether edit action is enabled
     *
     * @return bool
     */
    protected function isEditable()
    {
        return $this->is_editable;
    }

    /**
     * You can hide edit action in the main grid while the edit action is still enabled
     *
     * @param boolean $is_edit_action_displayed_in_grid
     * @return AdminCRUDController
     */
    protected function setEditActionDisplayedInGrid($is_edit_action_displayed_in_grid = true)
    {
        $this->is_edit_action_displayed_in_grid = $is_edit_action_displayed_in_grid;
        return $this;
    }

    /**
     * Tells whether edit action is displayed in grid
     *
     * @return bool
     */
    protected function isEditActionDisplayedInGrid()
    {
        return $this->is_edit_action_displayed_in_grid;
    }

    /**
     * Enables or disables add action
     *
     * @param boolean $is_addable
     * @return AdminCRUDController
     */
    protected function setAddable($is_addable = true)
    {
        $this->is_addable = $is_addable;
        return $this;
    }

    /**
     * Tells whether the controller allows adding an element
     *
     * @return bool
     */
    protected function isAddable()
    {
        return $this->is_addable;
    }

    /**
     * Enables or disables delete action
     *
     * @param boolean $is_deletable
     * @return AdminCRUDController
     */
    protected function setDeletable($is_deletable = true)
    {
        $this->is_deletable = $is_deletable;
        return $this;
    }

    /**
     * Tells whether the controller allows deleting an element
     *
     * @return bool
     */
    protected function isDeletable()
    {
        return $this->is_deletable;
    }

    /**
     * Enables preview action
     *
     * @param boolean $is_previewable
     * @return AdminCRUDController
     */
    protected function setPreviewable($is_previewable = true)
    {
        $this->is_previewable = $is_previewable;
        return $this;
    }

    /**
     * Tells whether the controller allows element preview
     *
     * @return bool
     */
    protected function isPreviewable()
    {
        return $this->is_previewable;
    }

    /**
     * Sets parameter to datagrid->generate( $parameter) function
     *
     * @param boolean $parameter
     * @return AdminCRUDController
     */
    protected function setDataGridGenerateParameter($parameter = false)
    {
        $this->datagrid_generate_parameter = $parameter;
        return $this;
    }

    /**
     * Enables or disables popup
     *
     * @param boolean $is_popup_enabled
     * @return AdminCRUDController
     */
    protected function setPopupEnabled($is_popup_enabled = true)
    {
        $this->is_popup_enabled = $is_popup_enabled;
        return $this;
    }

    /**
     * Tells whether the popup is enabled
     *
     * @return bool
     */
    protected function isPopupEnabled()
    {
        return $this->is_popup_enabled;
    }

    /**
     * Tells whether the current layout is a popup
     *
     * @return bool
     */
    protected function isLayoutPopup()
    {
        return $this->input->getParam('layout') == 'popup';
    }

    /**
     * Returns name of item order column
     *
     * @return String
     */
    protected function getItemOrderColumn()
    {
        return $this->item_order_column;
    }

    /**
     * Returns name of column used as a constraint for item order
     *
     * @return String
     */
    protected function getItemOrderConstraintColumn()
    {
        return $this->item_order_constraint_column;
    }

    /**
     * Sets table from which data will be pulled
     *
     * @param string $table
     * @return AdminCRUDController
     */
    protected function setTable($table)
    {
        $this->table = $table;
        $this->feed_object = false;
        return $this;
    }

    /**
     * Returns database table name used
     *
     * @return string
     */
    protected function getTable()
    {
        if ($this->getFeedObject()) {
            return $this->getFeedObject()->getTable();
        } else {
            return $this->table;
        }
    }

    /**
     * Sets ID field name
     *
     * @param string $id_field_name
     * @return AdminCRUDController
     */
    protected function setIdFieldName($id_field_name)
    {
        $this->id_field_name = $id_field_name;
        return $this;
    }

    /**
     * Returns ID field name
     *
     * @return string
     */
    protected function getIdFieldName()
    {
        if ($this->getFeedObject()) {
            return $this->getFeedObject()->getIdFieldName();
        } else {
            return $this->id_field_name;
        }
    }

    /**
     * Sets feed object
     *
     * @param $feed_object
     * @return AdminCRUDController
     */
    protected function setFeedObject($feed_object)
    {
        $this->feed_object = $feed_object;
        $this->table = false;
        $this->id_field_name = false;
        return $this;
    }

    /**
     * Returns feed object
     *
     * @return Generic_model
     */
    protected function getFeedObject()
    {
        return $this->feed_object;
    }

    /**
     * Set where conditions as associative array
     *
     * @param array $where
     * @return AdminCRUDController
     */
    protected function setWhere($where)
    {
        $this->where = $where;
        return $this;
    }

    /**
     * Returns associative array representing SQL where conditions
     *
     * @return array
     */
    protected function getWhere()
    {
        return $this->where;
    }

    /**
     * Sets page title
     *
     * @param string $title
     * @return AdminCRUDController
     */
    protected function setPageTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns page title
     *
     * @return string
     */
    protected function getPageTitle()
    {
        return $this->title;
    }

    /**
     * Sets add new item label
     *
     * @param string $add_new_item_label
     * @return AdminCRUDController
     */
    protected function setAddNewItemLabel($add_new_item_label)
    {
        $this->add_new_item_label = $add_new_item_label;
        return $this;
    }

    /**
     * Sets add new item label
     *
     * return string
     */
    protected function getAddNewItemLabel()
    {
        if ($this->add_new_item_label) {
            return $this->add_new_item_label;
        }

        return $this->lang->line('crud_label_add_item');
    }

    /**
     * Sets back to items label
     *
     * @param string $back_to_items_label
     * @return AdminCRUDController
     */
    protected function setBackToItemsLabel($back_to_items_label)
    {
        $this->back_to_items_label = $back_to_items_label;
        return $this;
    }

    /**
     * Returns back to items label
     *
     * return string
     */
    protected function getBackToItemsLabel()
    {
        if ($this->back_to_items_label) {
            return $this->back_to_items_label;
        }

        return $this->lang->line('crud_label_back_to_items');
    }

    // *************************************************************************
    // META
    // *************************************************************************

    /**
     * Sets the field name and label for the meta column ordering
     *
     * @param $meta_order_field
     * @param bool $meta_order_field_label
     * @return $this
     */
    protected function setMetaOrderField($meta_order_field, $meta_order_field_label = false)
    {
        $this->meta_order_field = $meta_order_field;
        $this->meta_order_field_label = $meta_order_field_label;
        return $this;
    }

    /**
     * Gets the field name and label for the meta column ordering
     *
     * @return array(meta_order_field,meta_order_field_label)
     */
    protected function getMetaOrderField()
    {
        return array(
            'meta_order_field' => $this->meta_order_field,
            'meta_order_field_label' => $this->meta_order_field_label,
        );
    }

    /**
     * Sets meta image field name and base url that will be prepended
     *
     * @param String $image_field
     * @param String $image_base_url
     * @return AdminCRUDController
     */
    protected function setMetaImageField($image_field, $image_base_url = '')
    {
        $this->meta_image_field = $image_field;
        $this->meta_image_base_url = $image_base_url;
        return $this;
    }

    /**
     * Sets title pattern
     *
     * Pattern is a text that contains references to row (line) variables
     * @example "User {name} is {age} years old
     *
     * @param String $title_pattern
     * @return AdminCRUDController
     */
    protected function setMetaTitlePattern($title_pattern)
    {
        if (!is_callable($title_pattern)) {
            $matches = array();
            preg_match_all('/{([a-z_0-9]+)}/', $title_pattern, $matches);
            $this->meta_title_pattern_keys = $matches[1];
        }

        $this->meta_title_pattern = $title_pattern;
        return $this;
    }

    /**
     * Returns meta title pattern
     *
     * @return String
     */
    protected function getMetaTitlePattern()
    {
        return $this->meta_title_pattern;
    }

    /**
     * Sets meta description pattern
     *
     * Pattern is a text that contains references to row (line) variables
     * @example "User {name} is {age} years old
     *
     * @param String $description_pattern
     * @param Callback $description_pattern_callback
     * @return AdminCRUDController
     */
    protected function setMetaDescriptionPattern($description_pattern, $description_pattern_callback = null)
    {
        if (!is_callable($description_pattern)) {
            $matches = array();
            preg_match_all('/{([a-z_0-9]+)}/', $description_pattern, $matches);
            $this->meta_description_pattern_keys = $matches[1];

            if (is_callable($description_pattern_callback)) {
                $this->setMetaDescriptionPatternCallback($description_pattern_callback);
            }

            $this->meta_description_pattern = $description_pattern;
        } else {
            // $description_pattern callable
            $this->setMetaDescriptionPatternCallback($description_pattern);
        }

        return $this;
    }

    /**
     * Returns meta description pattern
     *
     * @return String
     */
    protected function getMetaDescriptionPattern()
    {
        return $this->meta_description_pattern;
    }

    /**
     * Sets meta description pattern callback
     *
     * @param Callback $description_pattern_callback
     * @return AdminCRUDController
     */
    protected function setMetaDescriptionPatternCallback($description_pattern_callback)
    {
        $this->meta_description_pattern_callback = $description_pattern_callback;
        return $this;
    }

    /**
     * Returns meta description pattern
     *
     * @return bool|callable
     */
    protected function getMetaDescriptionPatternCallback()
    {
        return $this->meta_description_pattern_callback;
    }

    /**
     * Adds a new meta action by link and label patterns
     *
     * Pattern is a text that contains references to row (line) variables
     * @example "User {name} is {age} years old
     *
     * @param $link_pattern
     * @param $label_pattern
     * @param bool $css_class
     * @param null $is_popup
     * @param bool $target
     * @return $this
     */
    protected function addMetaAction($link_pattern, $label_pattern, $css_class = false, $is_popup = null, $target = false)
    {
        if (!is_callable($link_pattern)) {
            preg_match_all('/{([a-z_0-9]+)}/', $link_pattern, $matches_link);
        } else {
            $matches_link[1] = $link_pattern;
        }
        preg_match_all('/{([a-z_0-9]+)}/', $label_pattern, $matches_label);


        if ($is_popup === null) {
            $is_popup = $this->isPopupEnabled();
        }

        $this->datagrid_meta_actions[] = array(
            'link_pattern' => $link_pattern,
            'link_pattern_keys' => $matches_link[1],
            'label_pattern' => $label_pattern,
            'label_pattern_keys' => $matches_label[1],
            'css_class' => $css_class,
            'is_popup' => $is_popup,
            'target' => $target
        );

        return $this;
    }

    /**
     * Removes all options from meta actions
     * use on your own risk!
     *
     * @return AdminCRUDController
     */
    protected function removeMetaActions()
    {
        $this->datagrid_meta_actions = array();
        return $this;
    }

    /**
     * Removes all options from actions for index
     * use on your own risk!
     *
     * @return AdminCRUDController
     */
    protected function removeActionsForIndex()
    {
        $this->actions_for_index = array();
        return $this;
    }

    /**
     * Removes all options from actions for edit
     * use on your own risk!
     *
     * @return AdminCRUDController
     */
    protected function removeActionsForEdit()
    {
        $this->actions_for_edit = array();
        return $this;
    }

    /**
     * Sets related module
     * Experimental!
     *
     * @param String $related_module_name
     * @param String $related_module_filter_name
     * @deprecated as PepisCMS 0.2.4
     * @return AdminCRUDController
     */
    public function setRelatedModule($related_module_name, $related_module_filter_name)
    {
        trigger_error('AdminCRUDController::setRelatedModule() is deprecated.', E_USER_DEPRECATED);
        $this->related_module_name = $related_module_name;
        $this->related_module_filter_name = $related_module_filter_name;

        return $this;
    }

    /**
     * Returns the list of forced filers
     *
     * @return array
     */
    protected function getForcedFilters()
    {
        if (!$this->_forced_filters) {
            $this->_forced_filters = DataGrid::decodeFiltersString($this->input->getParam('forced_filters'));
        }

        return $this->_forced_filters;
    }

    /**
     * Sets forced filters for DataGrid. Note that if you call this method with
     * non-false argument, then the $_GET param will be ignored
     *
     * @param Array $_forced_filters_assoc
     * @return AdminCRUDController
     */
    protected function manuallySetForcedFilters($_forced_filters_assoc)
    {
        $this->_forced_filters = $_forced_filters_assoc;
        return $this;
    }

    /**
     * Returns an instance of Generic_model used for data manipulation
     *
     * @return Generic_model
     */
    protected function getModel()
    {
        if ($this->getFeedObject()) {
            return $this->getFeedObject();
        } else {
            $this->Generic_model->setTable($this->getTable());
            return $this->Generic_model;
        }
    }

    /**
     * DataGrid formatting function
     *
     * @param String $content
     * @param Object $line
     * @return string
     */
    public function _datagrid_format_id_metadata_column($content, &$line)
    {
        // TODO move HTML out of this method

        // $content is usually meta_order_field

        $id_field_name = &$this->id_field_name;
        $out = '<div class="nowrap">';
        if ($this->meta_image_field) {
            $is_real_image = false;

            $image_field_name = $this->meta_image_field;

            if (!is_array($this->meta_image_field) && isset($line->$image_field_name) && $line->$image_field_name) {
                // Determining image extension
                $image_extension = explode('.', $line->$image_field_name);
                if (count($image_extension) > 1) {
                    $image_extension = end($image_extension);
                    $image_extension = strtolower($image_extension);
                } else {
                    $image_extension = false;
                }

                $image_path = 'pepiscms/theme/img/ajaxfilemanager/broken_image_50.png';

                $is_real_image = false;
                if (in_array($image_extension, array('jpg', 'jpeg', 'png', 'bmp', 'tiff'))) {
                    $is_real_image = true;
                }

                if ($is_real_image) {
                    $image_path = 'admin/ajaxfilemanager/absolutethumb/50/' . $this->meta_image_base_url . $line->$image_field_name;
                } elseif (file_exists(APPPATH . '/../theme/file_extensions/file_extension_' . $image_extension . '.png')) {
                    $image_path = 'pepiscms/theme/file_extensions/file_extension_' . $image_extension . '.png';
                }

                $image_out = '<img class="image" data-src="' . $image_path . '" alt="" />';
            } elseif (is_callable($this->meta_image_field)) {
                $image_field_name = call_user_func_array($this->meta_image_field, array($content, &$line));
                if ($image_field_name) {
                    $image_out = '<img class="image" data-src="' . admin_url() . 'ajaxfilemanager/absolutethumb/50/' . $this->meta_image_base_url . $image_field_name . '" alt="" />';
                } else {
                    $image_out = '<img class="image" src="pepiscms/theme/img/dialog/datagrid/noimage_50.png" alt="" />';
                }
            } else {
                $image_out = '<img class="image" src="pepiscms/theme/img/dialog/datagrid/noimage_50.png" alt="" />';
            }

            $out .= '<div class="image' . (!$is_real_image ? ' image_like' : '') . '">' . $image_out . '</div>';
        }

        $out .= '<div class="details">';

        $title = $this->getCompiledTitle($content, $line);
        if ($title) {
            $out .= '<span class="title">' . htmlentities($title, ENT_COMPAT, 'UTF-8') . '</span>';
        }

        $out .= '<span class="description">';
        $description = $this->getCompiledDescription($content, $line);

        /* Actions */
        $action_out = '';
        $running_module = $this->getAttribute('running_module');
        if ($this->isPreviewable() && SecurityManager::hasAccess($running_module, 'preview', $running_module)) {
            $action_out .= '<a href="' . $this->getModuleBaseUrl() . 'preview/id-' . $line->$id_field_name . ($this->input->getParam('order_by') ? '/order_by-' . $this->input->getParam('order_by') : '') . ($this->input->getParam('order') ? '/order-' . $this->input->getParam('order') : '') . ($this->input->getParam('filters') ? '/filters-' . $this->input->getParam('filters') : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . '" class="preview ' . (($this->isPopupEnabled()) ? 'popup' : '') . '">' . $this->lang->line('crud_label_preview') . '</a>';
        }
        if ($this->isEditable() && $this->is_edit_action_displayed_in_grid && SecurityManager::hasAccess($running_module, 'edit', $running_module)) {
            $action_out .= '<a href="' . $this->getModuleBaseUrl() . 'edit/id-' . $line->$id_field_name . ($this->input->getParam('order_by') ? '/order_by-' . $this->input->getParam('order_by') : '') . ($this->input->getParam('order') ? '/order-' . $this->input->getParam('order') : '') . ($this->input->getParam('filters') ? '/filters-' . $this->input->getParam('filters') : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . '" class="edit ' . (($this->isPopupEnabled()) ? 'popup' : '') . '">' . $this->lang->line('crud_label_modify') . '</a>';
        }
        if ($this->isDeletable() && SecurityManager::hasAccess($running_module, 'delete', $running_module)) {
            $action_out .= '<a href="' . $this->getModuleBaseUrl() . 'delete/id-' . $line->$id_field_name . ($this->input->getParam('order_by') ? '/order_by-' . $this->input->getParam('order_by') : '') . ($this->input->getParam('order') ? '/order-' . $this->input->getParam('order') : '') . ($this->input->getParam('filters') ? '/filters-' . $this->input->getParam('filters') : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . '" class="ask_for_confirmation delete">' . $this->lang->line('crud_label_delete') . '</a>';
        }

        if ($this->isJournaleable() && SecurityManager::hasAccess($running_module, 'revisions', $running_module)) {
            $action_out .= '<a href="' . $this->getModuleBaseUrl() . 'revisions/id-' . $line->$id_field_name . ($this->input->getParam('order_by') ? '/order_by-' . $this->input->getParam('order_by') : '') . ($this->input->getParam('order') ? '/order-' . $this->input->getParam('order') : '') . ($this->input->getParam('filters') ? '/filters-' . $this->input->getParam('filters') : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . '" class="revisions popup">' . $this->lang->line('crud_revisions_show') . '</a>';
        }

        foreach ($this->datagrid_meta_actions as &$action) {
            if (is_callable($action['label_pattern'])) {
                $label = call_user_func_array($action['label_pattern'], array($content, &$line));
            } else {
                $label = PatternCompiler::compile($action['label_pattern'], $line, $action['label_pattern_keys']);
            }

            if (is_callable($action['link_pattern'])) {
                $link = call_user_func_array($action['link_pattern'], array($content, &$line));
                if (!$link) {
                    continue;
                }
            } else {
                $link = PatternCompiler::compile($action['link_pattern'], $line, $action['link_pattern_keys']);
            }


            $action_out .= '<a href="' . $link . '" class="' . $action['css_class'] . ' ' . ($action['is_popup'] ? ' popup' : '') . '" ' . ($action['target'] ? 'target="' . $action['target'] . '"' : '') . '>' . $label . '</a>';
        }

        if ($description) {
            $out .= htmlentities($description, ENT_COMPAT, 'UTF-8');
            if ($action_out) {
                $out .= ' - ';
            }
        }

        if ($action_out) {
            $out .= '<span class="separable">' . $action_out . '</span>';
        }


        $out .= '</span>' .
            '</div></div>';

        return $out;
    }

    /**
     * Returns compiled title for given line
     *
     * @param String $content
     * @param String $line
     * @return string
     */
    protected function getCompiledTitle($content, $line)
    {
        if (!$this->meta_title_pattern) {
            return '';
        }
        if (is_callable($this->meta_title_pattern)) {
            $title = call_user_func_array($this->meta_title_pattern, array($content, &$line));
        } else {
            $title = PatternCompiler::compile($this->meta_title_pattern, $line, $this->meta_title_pattern_keys);
        }

        return $title;
    }

    /**
     * Returns compiled description for given line
     *
     * @param String $content
     * @param String $line
     * @return string
     */
    protected function getCompiledDescription($content, $line)
    {
        if (!$this->meta_description_pattern && !$this->meta_description_pattern_callback) {
            return '';
        }

        if (!$this->meta_description_pattern) {
            // description_pattern_callback was previously defined
            $title = call_user_func_array($this->meta_description_pattern_callback, array($content, &$line));
        } else {
            $title = PatternCompiler::compile($this->meta_description_pattern, $line, $this->meta_description_pattern_keys);
            if ($this->meta_description_pattern_callback) {
                $title = call_user_func_array($this->meta_description_pattern_callback, array($title, &$line));
            }
        }

        return $title;
    }

    /**
     * Compiles pattern, if keys to be replaced are specified, then the script will not parse pattern (faster)
     *
     * @param $pattern
     * @param $object_with_data
     * @param array $keys_to_be_replaced
     * @return string
     * @deprecated as PepisCMS 1.0.0
     */
    protected static function compilePattern($pattern, $object_with_data, $keys_to_be_replaced = array())
    {
        trigger_error('AdminCRUDController::compilePattern() is deprecated.', E_USER_DEPRECATED);
        return PatternCompiler::compile($pattern, $object_with_data, $keys_to_be_replaced);
    }

    /**
     * Tells whether journaling is enabled
     *
     * @return bool
     */
    protected function isJournaleable()
    {
        // TODO implement Interface
        if (!method_exists($this->getModel(), 'getJournalingIsEnabled')) {
            return false;
        }

        return $this->getModel()->getJournalingIsEnabled() && $this->getModel()->getJournalingTable();
    }

    /**
     * Crud READ action
     * @param bool $display_view
     */
    public function index($display_view = true)
    {
        $running_module = $this->getAttribute('running_module');
        $definition = $this->getDefinition();

        // Removing keys that should not be displayed
        $forced_filters = $this->getForcedFilters();
        if (count($forced_filters)) {
            foreach ($forced_filters as $field_name => $value) {
                if (!isset($definition[$field_name])) {
                    continue;
                }
                unset($definition[$field_name]);
                $this->datagrid->applyFilter($field_name, $value);
            }
        }

        // Adding an extra virtual field containing metadata
        $definition = array_merge(array('id_metadata' => array(
            'field' => $this->meta_order_field,
            'label' => $this->meta_order_field_label,
            'grid_formating_callback' => array($this, '_datagrid_format_id_metadata_column'),
        )), $definition);


        // Adding arrows move up and down links for an orderable CRUD
        if ($this->isOrderable() && SecurityManager::hasAccess($running_module, 'move', $running_module)) {
            // Reading the label from datagrid definition
            $item_order_column_label = false;
            if (isset($definition[$this->item_order_column]['label'])) {
                $item_order_column_label = $definition[$this->item_order_column]['label'];
            }

            // Adding an extra virtual field
            $definition = array_merge($definition, array(array(
                'field' => $this->item_order_column,
                'label' => $item_order_column_label,
                'grid_formating_callback' => array($this, '_datagrid_format_item_order_column'),
                'grid_css_class' => 'medium',
            )));
        }

        // Adding starable link for a starable CRUD
        if ($this->isStarable()) {
            // If there was no label set, reading the label from datagrid definition
            if (!$this->is_stared_label) {
                if (isset($definition[$this->is_stared_column]['label'])) {
                    $this->is_stared_label = $definition[$this->is_stared_column]['label'];
                }
            }

            // Adding an extra virtual field
            $definition = array_merge($definition, array(array(
                'field' => $this->is_stared_column,
                'label' => $this->is_stared_label,
                'grid_formating_callback' => array($this, '_datagrid_format_is_stared_column'),
                'grid_css_class' => 'medium',
            )));
        }

        // Adding import link for importable CRUD
        if ($this->isImportable()) {
            $this->addActionForIndex(array('link' => $this->getModuleBaseUrl() . 'import', 'name' => $this->lang->line('crud_label_import_from_file'), 'icon' => module_resources_url('crud') . 'import_12.png'));
        }

        // Adding export links for exportable CRUD
        if ($this->isExportable()) {
            $this->addActionForIndex(array('link' => $this->getModuleBaseUrl() . 'export/format-csv', 'name' => $this->lang->line('crud_label_export') . ' (CSV)', 'icon' => module_resources_url('crud') . 'export_12.png'));
            $this->addActionForIndex(array('link' => $this->getModuleBaseUrl() . 'export/format-xls', 'name' => $this->lang->line('crud_label_export') . ' (XLS - Excel 97)', 'icon' => module_resources_url('crud') . 'export_12.png'));
            $this->addActionForIndex(array('link' => $this->getModuleBaseUrl() . 'export/format-xlsx', 'name' => $this->lang->line('crud_label_export') . ' (XLSX - Excel 2007)', 'icon' => module_resources_url('crud') . 'export_12.png'));
        }


        // Checking if there is a feed object available
        if ($this->getFeedObject()) {
            // Applying CRUD where to feed object if case
            if ($this->getWhere()) {
                $this->getFeedObject()->setWhere($this->getWhere());
            }

            // Seting datagrid feed object if case
            $this->datagrid->setFeedObject($this->getFeedObject());
        } // Setting table when there is no feed object
        else {
            $this->datagrid->setTable($this->getTable(), $this->getWhere());
        }

        // Sometimes redundant but for your own safety leave it like this
        $this->id_field_name = $this->datagrid->getFeedObject()->getIdFieldName();


        // Prevent glitchs related to FOREIGN_KEY_MANY_TO_MANY datagrid order mechanism
        foreach ($definition as &$field_definition) {
            // If the field is of relation FOREIGN_KEY_MANY_TO_MANY
            if (isset($field_definition['foreign_key_relationship_type']) && $field_definition['foreign_key_relationship_type'] == FormBuilder::FOREIGN_KEY_MANY_TO_MANY) {
                // If there was no grid_is_orderable value already set
                if (!isset($field_definition['grid_is_orderable'])) {
                    $field_definition['grid_is_orderable'] = false;
                }
            }
        }

        $this->datagrid->setTitle($this->getPageTitle());
        $this->datagrid->setBaseUrl($this->getModuleBaseUrl() . $this->getMethodName() . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : ''));
        $this->datagrid->setDefinition($definition);

        $this->assign('datagrid', $this->datagrid->generate($this->datagrid_generate_parameter));
        $this->assign('title', $this->getPageTitle());
        $this->assign('add_new_item_label', $this->getAddNewItemLabel());
        $this->assign('is_addable', $this->isAddable());
        $this->assign('tooltip_text', $this->tooltip_text_for_index);
        $this->assign('is_popup_enabled', $this->isPopupEnabled());
        $this->assign('actions', $this->actions_for_index);
        $this->assign('back_action_for_index', $this->back_action_for_index);
        $this->assign('module_name', $this->getModuleName());
        $this->assign('method_name', $this->getMethodName());
        $this->assign('module_base_url', $this->getModuleBaseUrl());

        $this->assign('forced_filters', $this->getForcedFilters());
        $this->assign('layout', $this->input->getParam('layout'));
        $this->assign('filters', $this->input->getParam('filters'));
        $this->assign('order', $this->input->getParam('order'));
        $this->assign('order_by', $this->input->getParam('order_by'));

        // Displaying
        if (!$display_view) {
            return;
        }

        if (file_exists($this->current_module_template_path . 'index.php')) {
            $this->display($this->current_module_template_path . 'index.php');
        } else {
            $this->display($this->getTemplatePath() . 'index.php');
        }
    }

    /**
     * Restore field action
     *
     * @return void
     */
    public function revisionrestorefield()
    {
        $id = $this->input->getParam('id');
        $revision_id = $this->input->getParam('revision_id');
        $field = $this->input->getParam('field');

        if (!$id || !$revision_id || !$field || !$this->isJournaleable()) {
            show_404();
        }

        $revision = $this->getModel()->journalingGetById($id, $revision_id);
        if (!$revision || !isset($revision->$field)) {
            show_404();
        }

        $data = array(
            $field => $revision->$field,
        );

        $success = $this->getModel()->saveById($id, $data);

        if ($this->input->getParam('layout') == 'popup') {
            if ($success) {
                return $this->_closePopupAndDisplaySuccess('crud_revision_field_successfully_restored');
            } else {
                $this->_closePopup();
            }
        } else {
            if ($success) {
                $this->load->library('SimpleSessionMessage');
                $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
                $this->simplesessionmessage->setMessage('crud_revision_field_successfully_restored');
            }

            redirect($this->getModuleBaseUrl() . 'revisions/id-' . $id . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : ''));
        }
    }

    /**
     * Revision preview action
     */
    public function revision()
    {
        $id = $this->input->getParam('id');
        $revision_id = $this->input->getParam('revision_id');

        if (!$id || !$revision_id || !$this->isJournaleable()) {
            show_404();
        }

        $revision = $this->getModel()->journalingGetById($id, $revision_id);
        $revision_current = $this->getModel()->journalingGetById($id);

        $key_names = array();
        $definition = $this->getDefinition();
        foreach ($definition as $key => $field_definition) {
            if (isset($field_definition['label'])) {
                $key_names[$key] = $field_definition['label'];
            }
        }

        // Detecting whether the file is identical
        $is_identical = true;
        foreach ($revision as $key => $value) {
            if ($revision_current->$key != $revision->$key) {
                $is_identical = false;
                break;
            }
        }

        $this->assign('id', $id);
        $this->assign('title', $this->getPageTitle());
        $this->assign('revision_id', $revision_id);
        $this->assign('revision', $revision);
        $this->assign('revision_current', $revision_current);
        $this->assign('key_names', $key_names);
        $this->assign('is_identical', $is_identical);
        $this->assign('module_name', $this->getModuleName());

        if (file_exists($this->current_module_template_path . 'revision.php')) {
            $this->display($this->current_module_template_path . 'revision.php');
        } else {
            $this->display($this->getTemplatePath() . 'revision.php');
        }
    }

    /**
     * Revisions list action
     */
    public function revisions()
    {
        $id = $this->input->getParam('id');

        if (!$id || !$this->isJournaleable()) {
            show_404();
        }

        $revision_summary = $this->getModel()->journalingGetRevisionSummary($id);
        $author = $this->db->select('author')->from($this->getTable())->where('id', $id)->get()->result();

        $this->assign('id', $id);
        $this->assign('title', $this->getPageTitle());
        $this->assign('revision_summary', $revision_summary);
        $this->assign('back_to_items_label', $this->getBackToItemsLabel());
        $this->assign('back_link_for_edit', $this->getBackLinkForEdit());
        $this->assign('module_name', $this->getModuleName());
        $this->assign('author', $author);

        if (file_exists($this->current_module_template_path . 'revisions.php')) {
            $this->display($this->current_module_template_path . 'revisions.php');
        } else {
            $this->display($this->getTemplatePath() . 'revisions.php');
        }
    }

    /**
     * CRUD create/update action
     * @param bool $display_view
     */
    public function edit($display_view = true)
    {
        $id = $this->input->getParam('id');

        if ($id && !$this->isEditable()) {
            show_404();
        }
        if (!$id && !$this->isAddable()) {
            show_404();
        }

        if ($id) {
            $this->formbuilder->setTitle($this->lang->line('crud_label_modify'));
        } else {
            $this->formbuilder->setTitle($this->getAddNewItemLabel());
        }


        if ($this->input->getParam('layout') == 'popup' && $this->input->getParam('direct')) {
            $this->formbuilder->setCallback(array($this, '_fb_callback_popup_close'), FormBuilder::CALLBACK_AFTER_SAVE);
        }

        //$this->formbuilder->setReadOnly( FALSE );
        $this->_edit(false, $display_view);
    }

    /**
     * This method is called by edit or preview
     * @param bool $is_preview
     * @param bool $display_view
     */
    protected function _edit($is_preview = false, $display_view = true)
    {
        $forced_filters = $this->getForcedFilters();
        $definition = $this->getDefinition();

        $id = $this->input->getParam('id');

        foreach ($forced_filters as $field_name => $value) {
            if (!isset($definition[$field_name])) {
                continue;
            }
            //unset($definition[$field_name]);
            $definition[$field_name]['input_is_editable'] = false;
            $definition[$field_name]['input_default_value'] = $value;
            $definition[$field_name]['filter_type'] = false;
        }

        $this->formbuilder->setId($id);
        $this->formbuilder->setBackLink($this->getBackLinkForEdit() . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : ''));
        $this->formbuilder->setDefinition($definition);

        if ($this->getFeedObject()) {
            $this->formbuilder->setFeedObject($this->getFeedObject());
        } else {
            $this->formbuilder->setTable($this->getTable(), $this->formbuilder->getFieldNames());
        }

        if ($id && $this->isJournaleable()) {
            $this->addActionForEdit(array('link' => $this->getModuleBaseUrl() . 'revisions/id-' . $id, 'name' => $this->lang->line('crud_revisions_show'), 'icon' => module_resources_url('crud') . 'revision_16.png', 'class' => 'popup'));
        }

        // Note: this must be called before getObject() method
        $form = $this->formbuilder->generate();

        // Showing 404 for wrong IDs for edit/preview pages
        if ($id && !$this->formbuilder->getObject()) {
            show_404();
        }

        $this->assign('form', $form);
        $this->assign('id', $id);
        $this->assign('is_preview', $is_preview);
        $this->assign('is_editable', $this->isEditable());
        $this->assign('is_previewable', $this->isPreviewable());
        $this->assign('title', $this->getPageTitle());
        $this->assign('back_to_items_label', $this->getBackToItemsLabel());
        $this->assign('back_link_for_edit', $this->getBackLinkForEdit());
        $this->assign('related_module_name', $this->related_module_name); // TODO Deprecated as PepisCMS 0.2.4.1
        $this->assign('related_module_filter_name', $this->related_module_filter_name); // TODO Deprecated as PepisCMS 0.2.4.1
        $this->assign('actions', $this->actions_for_edit);
        $this->assign('module_name', $this->getModuleName());

        if (!$is_preview) {
            // This should be defined after the form is generated - this allows to be changed using form callbacks
            $this->assign('tooltip_text', $this->tooltip_text_for_edit);
        }

        // Displaying
        if (!$display_view) {
            return;
        }

        if (file_exists($this->current_module_template_path . 'edit.php')) {
            $this->display($this->current_module_template_path . 'edit.php');
        } else {
            $this->display($this->getTemplatePath() . 'edit.php');
        }
    }

    /**
     * Export action
     */
    public function export()
    {
        if (!$this->isExportable()) {
            show_404();
        }

        $format = $this->input->getParam('format');
        if (!in_array($format, array('csv', 'xls', 'xlsx'))) {
            show_404();
        }

        $this->load->library('Spreadsheet');

        // getting data
        $result = $this->getModel()->getAdvancedFeed('*', 0, 999999, $this->getModel()->getIdFieldName(), 'ASC', array(), false);
        $result = $result[0];

        // caching variable, we dont want is_callable to be executed for every row
        $is_callable = is_callable($this->is_exportable_data_formatting_callback);
        $export_result = array();

        foreach ($result as &$line) {
            // transforming object into array
            $line_data = (array)$line;
            unset($line); // saving some memory

            // applying formatting callback if case
            if ($is_callable) {
                $line_data = call_user_func_array($this->is_exportable_data_formatting_callback, array($line_data));
            }

            // building export array
            $export_result[] = $line_data;
        }

        // exporting into desired format

        $file_name = ($this->getModel()->getTable() ? $this->getModel()->getTable() : $this->getModuleName()) . '-' . date('Y-m-d-h-i-s');

        if ($format == 'csv') {
            $this->spreadsheet->generateCSV($export_result, false, $file_name . '.csv');
        } elseif ($format == 'xls') {
            $this->spreadsheet->generateExcel($export_result, false, $file_name . '.xls', true, true, Spreadsheet::EXCEL_XLS);
        } elseif ($format == 'xlsx') {
            $this->spreadsheet->generateExcel($export_result, false, $file_name . '.xlsx', true, true, Spreadsheet::EXCEL_XLSX);
        }
    }

    /**
     * Import action
     */
    public function import()
    {
        if (!$this->isImportable()) {
            show_404();
        }

        $cache_path = $this->config->item('cache_path');
        $cache_path = ($cache_path === '') ? 'application/cache/' : $cache_path;
        if ($cache_path{0} !== '/') {
            $cache_path = INSTALLATIONPATH . $cache_path;
        }
        $cache_path .= 'tmp/';

        $this->load->library('FormBuilder');
        $this->load->library('Spreadsheet');


        $this->formbuilder->clear();
        $this->formbuilder->setTitle($this->lang->line('crud_label_import'));
        $this->formbuilder->setSubmitLabel($this->lang->line('crud_label_import_from_file'));
        $this->formbuilder->setCallback(array($this, '_fb_callback_on_import'), FormBuilder::CALLBACK_ON_SAVE);
        $this->formbuilder->setBackLink($this->getModuleBaseUrl()); // TODO Add applied filters to the back link, the same as edit

        $upload_allowed_types = 'csv';
        if ($this->spreadsheet->isFullyEnabled()) {
            $description_key = 'crud_file_to_import_description_fully_enabled';
            $upload_allowed_types .= '|xls|xlsx';
            $tip_key = 'crud_label_import_tip_fully_enabled';
        } else {
            $description_key = 'crud_file_to_import_description';
            $tip_key = 'crud_label_import_tip';
        }

        $this->formbuilder->setDefinition(array(
            'file_to_import' => array(
                'upload_path' => $cache_path,
                'show_in_grid' => false,
                'show_in_form' => true,
                'input_type' => FormBuilder::FILE,
                'upload_allowed_types' => $upload_allowed_types,
                'validation_rules' => '',
                'label' => $this->lang->line('crud_file_to_import'),
                'description' => $this->lang->line($description_key),
            ),
            'commit' => array(
                'input_type' => FormBuilder::HIDDEN,
                'input_default_value' => 1,
            )
        ));

        $this->assign('tip', sprintf($this->lang->line($tip_key), implode(', ', $this->getModel()->getAcceptedPostFields()), $this->getModel()->getIdFieldName()));
        $this->assign('title', $this->getPageTitle());
        $this->assign('back_to_items_label', $this->getBackToItemsLabel());
        $this->assign('back_link_for_edit', $this->getBackLinkForEdit());
        $this->assign('formbuilder', $this->formbuilder->generate());
        $this->assign('module_name', $this->getModuleName());

        if (file_exists($this->current_module_template_path . 'import.php')) {
            $this->display($this->current_module_template_path . 'import.php');
        } else {
            $this->display($this->getTemplatePath() . 'import.php');
        }
    }

    /**
     * CRUD read
     * @param bool $display_view
     */
    public function preview($display_view = true)
    {
        if (!$this->isPreviewable()) {
            show_404();
        }

        $this->formbuilder->setTitle($this->lang->line('crud_label_preview'));
        $this->formbuilder->setReadOnly();
        $this->_edit(true, $display_view);
    }

    /**
     * Moves items by changing their item order
     */
    public function move()
    {
        $direction = $this->input->getParam('direction') == 'down' ? 'down' : 'up';
        $id = $this->input->getParam('id');

        if (!$id) {
            show_404();
        }

        $success = $this->getModel()->move($id, $direction, $this->getTable(), $this->getItemOrderConstraintColumn(), $this->getItemOrderColumn(), $this->getIdFieldName());

        if ($this->input->getParam('json') == 1) {
            if ($success) {
                die(json_encode(array('status' => 1, 'message' => 'OK')));
            } else {
                die(json_encode(array('status' => 0, 'message' => 'No change')));
            }
        }

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect($this->getModuleBaseUrl());
        }
    }

    /**
     * Deleting item
     */
    public function delete()
    {
        $id = $this->input->getParam('id');

        if (!$id || !$this->is_deletable) {
            show_404();
        }

        // getting full item to execute full delete procedure
        $item = $this->getModel()->getById($id);
        $this->_onDelete($id, $item);

        $success = $this->getModel()->deleteById($id);

        if ($this->input->getParam('json') == 1) {
            if ($success) {
                die(json_encode(array('status' => 1, 'message' => 'OK')));
            } else {
                die(json_encode(array('status' => 0, 'message' => 'Unable to delete')));
            }
        }

        if ($success) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
            $this->simplesessionmessage->setMessage('global_header_success');
        } else {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_WARNING);
            $this->simplesessionmessage->setMessage('crud_unable_to_delete');
        }

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect($this->getModuleBaseUrl());
        };
    }

    /**
     * Sets the star flag to TRUE/FALSE
     */
    public function star()
    {
        $id = $this->input->getParam('id');

        if (!$id || !$this->is_starable) {
            show_404();
        }

        $is_stared = $this->input->getParam('toggle');

        if ($is_stared != 1) {
            $is_stared = 0;
        }

        // Updating
        $this->db->set($this->is_stared_column, $is_stared)
            ->where($this->getIdFieldName(), $id)
            ->update($this->getTable());

        if ($this->input->getParam('json') == 1) {
            die(json_encode(array('status' => 1, 'message' => 'OK')));
        }

        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage('global_header_success');

        // Smart redirect
        $this->load->library('User_agent');
        if ($this->agent->referrer()) {
            redirect($this->agent->referrer());
        } else {
            redirect($this->getModuleBaseUrl());
        }
    }

    /**
     * Handles import file upload and parsing
     *
     * @param array $data_array
     * @return bool
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function _fb_callback_on_import($data_array)
    {
        $this->load->library('Spreadsheet');
        $this->load->helper('url');

        // checking if there was a file submited
        if (!isset($data_array['file_to_import']) || !$data_array['file_to_import']) {
            $this->formbuilder->setValidationErrorMessage($this->lang->line('crud_no_file_uploaded_or_wrong_filetype'));
            return false;
        }

        // building file path
        $cache_path = $this->config->item('cache_path');
        $cache_path = ($cache_path === '') ? 'application/cache/' : $cache_path;
        if ($cache_path{0} !== '/') {
            $cache_path = INSTALLATIONPATH . $cache_path;
        }
        $cache_path .= 'tmp/';

        $file = $cache_path . $data_array['file_to_import'];

        //file_put_contents($file, iconv('ASCII', 'UTF-8//IGNORE', file_get_contents($file)));

        // just in case
        if (!file_exists($file)) {
            $this->formbuilder->setValidationErrorMessage($this->lang->line('crud_imported_file_does_not_exist'));
            return false;
        }

        // parsing file based on file extension
        $ext_arts = explode('.', $file);
        $ext = strtolower(end($ext_arts));
        if ($ext == 'xls' || $ext == 'xlsx') {
            if (!$this->spreadsheet->isFullyEnabled()) {
                $this->formbuilder->setValidationErrorMessage($this->lang->line('crud_import_excel_not_fully_enabled'));
                return false;
            } else {
                $result = $this->spreadsheet->parseExcel($file);
            }
        } elseif ($ext == 'csv') {
            $result = $this->spreadsheet->parseCSV($file);
        }

        // no longer needed, security reasons
        unlink($file);

        // empty file, show warning
        if (!count($result)) {
            $this->formbuilder->setValidationErrorMessage(sprintf($this->lang->line('crud_import_empty_spreadsheet'), $ext));
            return false;
        }

        // initializing empty arrays
        $update_data = $insert_data = array();
        // we need it to filter user input
        $accepted_fields = $this->getModel()->getAcceptedPostFields();
        // we need it to decide whether to use insert or update queries
        $id_field_name = $this->getModel()->getIdFieldName();
        // We need it for updates as well
        $accepted_fields[] = $id_field_name;

        // caching variable, we dont want is_callable to be executed for every row
        $is_callable = is_callable($this->is_importable_data_formatting_callback);

        // for every line of submited data
        foreach ($result as $line) {
            $data = array();

            // automatic adjustments
            foreach ($accepted_fields as $field_name) {
                if (isset($line[$field_name])) {
                    $data[$field_name] = trim($line[$field_name]);

                    if (in_array($field_name, array('www', 'website', 'url', 'web_site'))) {
                        $data[$field_name] = prep_url($data[$field_name]);
                    }
                    if (in_array($field_name, array('email', 'e_mail'))) {
                        $data[$field_name] = strtolower($data[$field_name]);
                    }
                }
            }

            // filtering input, there are some fields that must be set, we do not care about the rest
            if ($this->is_importable_must_have_fields && is_array($this->is_importable_must_have_fields)) {
                $continue = false;
                foreach ($this->is_importable_must_have_fields as $must_have_field) {
                    if (!isset($data[$must_have_field]) || !strlen($data[$must_have_field])) {
                        $continue = true;
                        break;
                    }
                }
                if ($continue) {
                    continue;
                }
            }

            // callback modifier
            if ($is_callable) {
                $data = call_user_func_array($this->is_importable_data_formatting_callback, array($data, &$line));
            }

            // extra protection
            if (!$data) {
                continue;
            }

            // preparing data for separate insert and update queries
            if (isset($data[$id_field_name]) && $data[$id_field_name]) {
                $update_data[] = $data;
            } else {
                foreach ($accepted_fields as $field_name) {
                    if (!isset($data[$field_name])) {
                        $data[$field_name] = null;
                    }
                }
                $insert_data[] = $data;
            }
        }

        $count_insert = count($insert_data);
        $count_update = count($update_data);

        $affected_rows = 0;

        // doing batch insert
        if ($count_insert > 0) {
            $this->db->insert_batch($this->getModel()->getTable(), $insert_data);
            $affected_rows += $this->db->affected_rows();
        }

        // doing batch update
        if ($count_update > 0) {
            $this->db->update_batch($this->getModel()->getTable(), $update_data, $id_field_name);
            $affected_rows += $this->db->affected_rows();
        }

        if (($count_insert + $count_update) == 0) {
            $this->formbuilder->setValidationErrorMessage($this->lang->line('crud_import_nonempry_spreadsheet_but_nothing_to_update'));
            return false;
        }

        if ($affected_rows == 0) {
            $this->formbuilder->setValidationErrorMessage(sprintf($this->lang->line('crud_import_no_rows_affected'), $count_insert, $count_update));
            return false;
        }

        // great
        return true;
    }

    /**
     * Closing action for popup windows
     *
     * @param $data
     */
    public function _fb_callback_popup_close(&$data)
    {
        return $this->_closePopupAndDisplaySuccess();
    }

    /**
     * Prints popup close code and sets session message
     *
     * @param string $message
     */
    public function _closePopupAndDisplaySuccess($message = 'global_header_success')
    {
        $this->load->library('SimpleSessionMessage');
        $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
        $this->simplesessionmessage->setMessage($message);

        $this->_closePopup();
    }

    /**
     * Prints popup close code
     * @param bool $return
     * @return string
     */
    protected function _closePopup($return = false)
    {
        $out = ob_get_contents();
        if ($out) {
            die(); // This is not necessarely but helps to debug the system in case there are errors on save
        }

        $this->load->helper('popup');
        $out = popup_close_html();

        if ($return) {
            return $out;
        }

        // Else
        die($out);
    }

    /**
     * Overwrite this function if you want to remove extra items such as files
     * @param $id
     * @param $item
     */
    public function _onDelete($id, $item)
    {
        // By default this method is empty, it should be overwritten by inheriting class
    }

    /**
     * Datagrid callback
     *
     * @param String $content
     * @param Object $line
     * @return String html
     */
    public function _datagrid_format_is_stared_column($content, &$line)
    {
        $id_field_name = &$this->id_field_name;
        if ($content == 1) {
            return '<a href="' . $this->getModuleBaseUrl() . 'star/id-' . $line->$id_field_name . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . '" class="toggle off"><span>x</span></a>';
        }
        return '<a href="' . $this->getModuleBaseUrl() . 'star/id-' . $line->$id_field_name . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . '" class="toggle on"><span>v</span></a>';
    }

    /**
     * Datagrid callback
     *
     * @param String $content
     * @param Object $line
     * @return String html
     */
    public function _datagrid_format_item_order_column($content, &$line)
    {
        $id_field_name = &$this->id_field_name;

        $o = $this->datagrid->getOrder();
        $order_by = $o['order_by'];
        $order = $o['order'];

        if (($order_by == $this->getItemOrderColumn() || $order_by == '') && count($this->datagrid->getFilterPairs()) == 0) {
            if ($order == 'ASC' || $order == '') {
                $up = 'up';
                $down = 'down';
            } else {
                $up = 'down';
                $down = 'up';
            }
            return '<a href="' . $this->getModuleBaseUrl() . '/move/id-' . $line->$id_field_name . '/direction-' . $up . ($this->getForcedFilters() ? '/forced_filters-' . DataGrid::encodeFiltersString($this->getForcedFilters()) : '') . ($this->input->getParam('layout') ? '/layout-' . $this->input->getParam('layout') : '') . '" class="moveUp json"><img src="pepiscms/theme/img/dialog/datagrid/up_16.png" alt="" /></a> <a href="' . $this->getModuleBaseUrl() . '/move/id-' . $line->$id_field_name . '/direction-' . $down . '" class="moveDown json"><img src="pepiscms/theme/img/dialog/datagrid/down_16.png" alt="" /></a>';
        } else {
            return $content;
        }
    }
}
