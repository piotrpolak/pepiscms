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
 * FormBuilder - utility for generating web forms
 *
 * Form builder handles form generation, validation and saving.
 * It can read data from any source that implements Entitable interface.
 *
 * @since 0.1.4
 *
 */
class FormBuilder extends ContainerAware
{
    const POST_ID_FIELD_NAME = 'form_builder_id';

    /**
     * Textfield input
     * @var int
     */
    const TEXTFIELD = 0;

    /**
     * Checkbox
     * @var int
     */
    const CHECKBOX = 1;

    /**
     * Selectbox
     * @var int
     */
    const SELECTBOX = 2;

    /**
     * Radio button
     * @var int
     */
    const RADIO = 3;

    /**
     * Textarea input
     * @var int
     */
    const TEXTAREA = 4;

    /**
     * Image field
     * @var int
     */
    const IMAGE = 5;

    /**
     * Rich text editor
     * @var int
     */
    const RTF = 6;

    /**
     * Rich text editor
     * @var int
     */
    const RTF_FULL = 66;

    /**
     * Multiple items can be checked
     * @var int
     */
    const MULTIPLESELECT = 7;

    /**
     * Hidden field
     * @var int
     */
    const HIDDEN = 8;

    /**
     * Date field
     * @var int
     */
    const DATE = 9;

    /**
     * File field
     * @var int
     */
    const FILE = 10;

    /**
     * Password field
     * @var int
     */
    const PASSWORD = 11;

    /**
     * Multiple radios, one can be checked
     * @var int
     */
    const MULTIPLECHECKBOX = 12;

    /**
     * Textfield with autocomplete
     * @var int
     */
    const TEXTFIELD_AUTOCOMPLETE = 14;

    /**
     * Selectbox with autocomplete
     */
    const SELECTBOX_AUTOCOMPLETE = 15;

    /**
     * Line break for floating forms, not really an input
     */
    const LINE_BREAK = 16;

    /**
     * Timestamp input
     */
    const TIMESTAMP = 17;

    /**
     * Colorpicker input
     */
    const COLORPICKER = 18;

    /**
     * This callback is called after retrieving the data but before rendering the form.
     * The callback function takes must take the OBJECT parameter as reference.
     *
     * @var int
     */
    const CALLBACK_BEFORE_RENDER = 500;

    /**
     * This callback is called before saving the data.
     * The callback function must take the ARRAY parameter as reference.
     *
     * @var int
     */
    const CALLBACK_BEFORE_SAVE = 501;

    /**
     * This callback is called after saving the data.
     * The callback function must take the ARRAY parameter as reference.
     *
     * @var int
     */
    const CALLBACK_AFTER_SAVE = 502;

    /**
     * This callback is called when data save fails. It can be using for rollback operations.
     * The callback function takes must take the ARRAY parameter as reference.
     *
     * @var int
     */
    const CALLBACK_ON_SAVE_FAILURE = 503;

    /**
     * This callback is called on save.
     * This kind of callback should be used when no feed object specified of when you want to overwrite the default SAVE operation.
     * The callback function takes must take the ARRAY parameter as reference and MUST return TRUE or FALSE.
     * If the function returns FALSE, it should also set FormBuilder validation error message.
     *
     * @var int
     */
    const CALLBACK_ON_SAVE = 504;

    /**
     * This callback is called on read.
     * This kind of callback should be used when no feed object specified of when you want to overwrite the default READ operation.
     * The callback function takes must take the OBJECT parameter as reference and to FILL it.
     * The callback does not need to return anything.
     *
     * @var int
     */
    const CALLBACK_ON_READ = 505;

    /**
     * Database ONE TO MANY relation constant
     *
     * @var int
     */
    const FOREIGN_KEY_ONE_TO_MANY = 600;

    /**
     * Database MANY TO MANY relation constant
     *
     * @var int
     */
    const FOREIGN_KEY_MANY_TO_MANY = 601;

    /**
     * Table title
     * @var string
     */
    private $title;

    /**
     * Object from which the data object will be extracted
     * @var object
     */
    private $feed_object;

    /**
     * Form rendering object
     * @var object
     */
    private $renderer;

    /**
     * List of fields
     * @var array
     */
    private $fields;

    /**
     * List of input groups, associative array
     * @var array
     */
    private $input_groups;

    /**
     * List of upload fields
     * @var array
     */
    private $file_upload_fields;

    /**
     * ID of the entity
     * @var int
     */
    private $id;

    /**
     * Unique form instance id
     * @var string
     */
    private $instance_code;

    /**
     * When set to true, the form is displayed in read-only mode
     * @var bool
     */
    private $read_only;

    /**
     * The object containing fields values
     * @var bool|object
     */
    private $object;

    /**
     * The URL displayed as back link
     * @var bool|string
     */
    private $back_link;

    /**
     * List of callbacks
     * @var bool
     */
    private $callbacks;

    /**
     * Upload real hdd path
     * @var string
     */
    private $default_uploads_path;

    /**
     * Upload display (web) path
     * @var string
     */
    private $default_upload_display_path;

    /**
     * Validation message container
     * @var string
     */
    private $validation_error_message;

    /**
     * Indicates whether the library should redirect user to the back URL on save success
     * @var bool
     */
    private $redirect_on_save_success;

    /**
     * Indicates whether the library should set simple session message on success
     * @var bool
     */
    private $use_simple_session_message_on_save_success;

    /**
     * Indicates whether the apply button is enabled
     * @var bool
     */
    private $is_apply_button_enabled;

    /**
     * Indicates whether the the default save button is enabled
     * @var bool
     */
    private $is_submit_button_enabled;

    /**
     * Form action, default itself
     * @var string
     */
    private $form_action;

    /**
     * Submit button label
     * @var string
     */
    private $form_submit_label;

    /**
     * When set to false, the old files are not removed when overwritten by new ones
     * @var bool
     */
    private $is_overwrite_files_on_upload;

    /**
     * Contains a list of upload warnings
     * @var array
     */
    private $upload_warnings;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->load->language('formbuilder');
        $this->load->library('form_validation');
        $this->load->library('SimpleSessionMessage');
        $this->load->library('upload');
        $this->clear();
    }

    /**
     * Resets the FormBuilder internal data
     *
     * @return FormBuilder
     */
    public function clear()
    {
        $this->callbacks = array();
        $this->is_overwrite_files_on_upload = true;
        $this->form_submit_label = false;
        $this->form_action = '';
        $this->is_submit_button_enabled = true;
        $this->is_apply_button_enabled = false;
        $this->use_simple_session_message_on_save_success = true;
        $this->redirect_on_save_success = true;
        $this->validation_error_message = false;
        $this->back_link = false;
        $this->object = false;
        $this->read_only = false;
        $this->instance_code = null;
        $this->id = false;
        $this->file_upload_fields = array();
        $this->input_groups = array();
        $this->fields = array();
        $this->renderer = null;
        $this->feed_object = false;
        $this->title = false;
        $this->default_uploads_path = $this->default_upload_display_path = $this->config->item('uploads_path');
        $this->upload_warnings = array();

        return $this;
    }

    /**
     * Returns the list of possible input types
     *
     * @return array
     */
    public static function getInputTypes()
    {
        $types = array();

        $types[self::TEXTFIELD] = 'TEXTFIELD';
        $types[self::CHECKBOX] = 'CHECKBOX';
        $types[self::SELECTBOX] = 'SELECTBOX';
        $types[self::RADIO] = 'RADIO';
        $types[self::TEXTAREA] = 'TEXTAREA';
        $types[self::IMAGE] = 'IMAGE';
        $types[self::RTF] = 'RTF';
        $types[self::RTF_FULL] = 'RTF_FULL';
        $types[self::MULTIPLESELECT] = 'MULTIPLESELECT';
        $types[self::HIDDEN] = 'HIDDEN';
        $types[self::DATE] = 'DATE';
        $types[self::FILE] = 'FILE';
        $types[self::PASSWORD] = 'PASSWORD';
        $types[self::MULTIPLECHECKBOX] = 'MULTIPLECHECKBOX';
        $types[self::TEXTFIELD_AUTOCOMPLETE] = 'TEXTFIELD_AUTOCOMPLETE';
        $types[self::SELECTBOX_AUTOCOMPLETE] = 'SELECTBOX_AUTOCOMPLETE';
        //$types[self::LINE_BREAK] = 'LINE_BREAK'; // Not really an input
        $types[self::TIMESTAMP] = 'TIMESTAMP';

        return $types;
    }

    /**
     * Returns a list of upload warnings
     *
     * @return array
     */
    public function getUploadWarnings()
    {
        return $this->upload_warnings;
    }

    /**
     * Sets the table title
     *
     * @param string $title
     * @return FormBuilder
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
     * Sets form action
     *
     * @param string $action
     * @return FormBuilder
     */
    public function setAction($action)
    {
        $this->form_action = $action;
        return $this;
    }

    /**
     * Returns the form action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->form_action;
    }

    /**
     * Sets submit label
     *
     * @param string $label
     * @return FormBuilder
     */
    public function setSubmitLabel($label)
    {
        $this->form_submit_label = $label;
        return $this;
    }

    /**
     * Returns submit label
     *
     * @return string
     */
    public function getSubmitLabel()
    {
        if (!$this->form_submit_label) {
            $this->form_submit_label = $this->lang->line('global_button_save_and_close');
        }

        return $this->form_submit_label;
    }

    /**
     * Enable or disable default submit, default is TRUE
     *
     * @param bool $is_submit_button_enabled
     * @return FormBuilder
     */
    public function setSubmitButtonEnabled($is_submit_button_enabled = true)
    {
        $this->is_submit_button_enabled = $is_submit_button_enabled;
        return $this;
    }

    /**
     * Returns true if the default submit button is enabled
     *
     * @return bool
     */
    public function isSubmitButtonEnabled()
    {
        return $this->is_submit_button_enabled;
    }

    /**
     * Enable or disable apply button, default is FALSE
     *
     * @param bool $is_apply_button_enabled
     * @return FormBuilder
     */
    public function setApplyButtonEnabled($is_apply_button_enabled = true)
    {
        $this->is_apply_button_enabled = $is_apply_button_enabled;
        return $this;
    }

    /**
     * Returns true if apply button is enabled
     *
     * @return bool
     */
    public function isApplyButtonEnabled()
    {
        return $this->is_apply_button_enabled;
    }

    /**
     * Tells whether the apply action has been fired
     *
     * @return bool
     */
    public function isApplyEventFired()
    {
        return ($this->input->get('apply', null) !== null
            && $this->input->get('apply') == '');
    }

    /**
     * Returns the list of input groups
     *
     * @return array
     */
    public function getInputGroups()
    {
        return $this->input_groups;
    }

    /**
     * Returns form definition, alias to getDefinition()
     *
     * @return array
     */
    public function getFields()
    {
        return $this->getDefinition();
    }

    /**
     * Returns form definition
     *
     * @return array
     */
    public function getDefinition()
    {
        return $this->fields;
    }

    /**
     * Returns form definition
     *
     * @param string $field_name
     * @return array
     */
    public function getField($field_name)
    {
        return (isset($this->fields[$field_name]) ? $this->fields[$field_name] : false);
    }

    /**
     * Returns field names
     *
     * @return array
     */
    public function getFieldNames()
    {
        $out = array();
        foreach ($this->fields as &$field) {
            $out[] = $field['field'];
        }
        return $out;
    }

    /**
     * Adds a definition of a new field
     *
     * @param array $field
     * @param bool $label
     * @param bool $type
     * @param bool $default_value
     * @param bool $rules
     * @param bool $values
     * @return FormBuilder
     */
    public function addField($field, $label = false, $type = false, $default_value = false, $rules = false, $values = false)
    {
        // If the first element is array, then setting field by definition
        if (is_array($field)) {
            return $this->addFieldByDefinition($field);
        }

        if ($type === false) {
            $type = FormBuilder::TEXTFIELD;
        }
        if ($rules === false) {
            $rules = 'required';
        }

        $field_definition = array(
            'field' => $field,
            'label' => $label,
            'input_type' => $type,
            'input_default_value' => $default_value,
            'values' => $values,
            'validation_rules' => $rules
        );

        return $this->addFieldByDefinition($field_definition);
    }

    /**
     * Returns field default configuration.
     * This method should be static.
     */
    public function getFieldDefaults()
    {
        $defaults = array();
        $defaults['field'] = false; // Field name
        $defaults['label'] = false; // Field label
        $defaults['description'] = false; // Field description
        // Display options
        $defaults['show_in_form'] = true; // Display in form?
        $defaults['show_in_grid'] = true; // Display in grid?
        // Foreign key
        $defaults['foreign_key_relationship_type'] = FormBuilder::FOREIGN_KEY_ONE_TO_MANY;
        $defaults['foreign_key_table'] = false;
        $defaults['foreign_key_field'] = 'id';
        $defaults['foreign_key_label_field'] = 'id';
        $defaults['foreign_key_accept_null'] = false;
        $defaults['foreign_key_where_conditions'] = false;

        $defaults['foreign_key_junction_table'] = false;
        $defaults['foreign_key_junction_id_field_left'] = false;
        $defaults['foreign_key_junction_id_field_right'] = false;
        $defaults['foreign_key_junction_where_conditions'] = false;

        //
        // Input specific
        //
        $defaults['input_type'] = FormBuilder::TEXTFIELD; // Input type, see FormBuilder constants
        $defaults['input_default_value'] = false; // Default value for field
        $defaults['values'] = false; // Values for to select among them, must be an associative array
        $defaults['validation_rules'] = 'required'; // Validation rules
        $defaults['input_is_editable'] = true;
        $defaults['input_group'] = false;
        $defaults['input_css_class'] = false;
        $defaults['options'] = array();

        // File upload
        $defaults['upload_complete_callback'] = false;
        $defaults['upload_path'] = $this->default_uploads_path;
        $defaults['upload_display_path'] = $this->default_upload_display_path;
        $defaults['upload_allowed_types'] = false;
        $defaults['upload_encrypt_name'] = false;

        //
        // Grid specific
        //
        $defaults['grid_formating_callback'] = false;
        $defaults['grid_is_orderable'] = true;
        $defaults['grid_css_class'] = false;
        $defaults['filter_type'] = false;
        $defaults['filter_values'] = false;
        $defaults['filter_condition'] = 'like';

        // Autocomplete
        $defaults['autocomplete_source'] = '';

        return $defaults;
    }

    /**
     * Sets whether the the old files are not removed when overwritten by new ones
     *
     * @param bool $is_overwrite_files_on_upload
     * @return FormBuilder
     */
    public function setOverwriteFilesOnUpload($is_overwrite_files_on_upload = true)
    {
        $this->is_overwrite_files_on_upload = $is_overwrite_files_on_upload;
        return $this;
    }

    /**
     * Tells whether the the old files are not removed when overwritten by new ones
     *
     * @return bool
     */
    public function isOverwriteFilesOnUpload()
    {
        return $this->is_overwrite_files_on_upload;
    }

    /**
     * Sets fields by definition
     *
     * @param array $fields
     * @return FormBuilder
     */
    public function setDefinition($fields)
    {
        // Resetting values
        $this->fields = array();
        $this->input_groups = array();

        foreach ($fields as $key => &$field) {
            // Make it work with associative
            if ($key && (!isset($field['field']) || $field['field'] === false)) {
                $field['field'] = $key;
            }
            $this->addFieldByDefinition($field);
        }

        return $this;
    }

    /**
     * Sets field by array definition
     * @param array $field
     * @return FormBuilder
     */
    public function addFieldByDefinition($field)
    {
        if (!isset($field['field']) || !$field['field']) {
            return false;
        }

        $defaults = $this->getFieldDefaults();
        foreach ($defaults as $name => $value) {
            if (is_callable($value)) {
                continue;
            }

            if (isset($field[$name])) {
                $defaults[$name] = $field[$name];
            }
            unset($field[$name]); // Saving memory and preventing strange errors when some keys have null value
        }

        if (!$defaults['show_in_form']) {
            return $this;
        }

        // Useful for debugging, prevents from using misspelled keys
        $unused_keys = array_keys($field);
        if (count($unused_keys)) {
            foreach ($unused_keys as $unused_key) {
                trigger_error('FormBuilder definition contains unknown key: ' . $unused_key, E_USER_NOTICE);
            }
        }

        // Input groups
        if (!$defaults['input_group']) {
            $defaults['input_group'] = 'default';
        }
        if (!isset($this->input_groups[$defaults['input_group']])) {
            $this->input_groups[$defaults['input_group']] = array(
                'label' => ucfirst(str_replace('_', ' ', $defaults['input_group'])),
                'description' => false,
                'fields' => array()
            );
        }
        $this->input_groups[$defaults['input_group']]['fields'][] = $defaults['field'];


        if ($defaults['input_type'] == FormBuilder::IMAGE || $defaults['input_type'] == FormBuilder::FILE) {
            $this->file_upload_fields[$defaults['field']] = $defaults['field'];
            $defaults['validation_rules'] = '';
            if ($defaults['upload_allowed_types'] === false && $defaults['input_type'] == FormBuilder::IMAGE) {
                $defaults['upload_allowed_types'] = 'jpg|jpeg|gif|png|bmp';
            }
        }

        $defaults['label'] = $defaults['label'] !== false ?
            $defaults['label'] : ucfirst(str_replace('_', ' ', $defaults['field']));

        $this->fields[$defaults['field']] = $defaults;

        return $this;
    }

    /**
     * Sets the feed object implementing feedable interface
     *
     * @param EntitableInterface $feed_object
     * @return FormBuilder
     */
    public function setFeedObject(EntitableInterface $feed_object)
    {
        $this->feed_object = $feed_object;
        return $this;
    }

    /**
     * Returns feed object used by formbuilder
     *
     * @return EntitableInterface
     */
    public function getFeedObject()
    {
        return $this->feed_object;
    }

    /**
     * Sets table used by default Generic_model
     *
     * @param string $tableName
     * @param array $acceptedPostFields
     * @param bool|string $idFieldName
     * @return FormBuilder
     */
    public function setTable($tableName, $acceptedPostFields = array(), $idFieldName = false)
    {
        // Initializes a cloned Generic Model with a specified table
        $feed_object = clone $this->Generic_model;
        $feed_object->setTable($tableName);

        // Specify id field name, otherwise use the default value from Generic Model
        if ($idFieldName) {
            $feed_object->setIdFieldName($idFieldName);
        }

        $feed_object->setAcceptedPostFields($acceptedPostFields);
        $this->setFeedObject($feed_object);
        return $this;
    }

    /**
     * Adds accepted post field, useful in callbacks
     *
     * @param string $field
     * @return FormBuilder
     * @throws Exception
     */
    public function addAcceptedPostField($field)
    {
        if ($this->feed_object instanceof Generic_model) {
            $this->feed_object->addAcceptedPostField($field);
            return $this;
        }
        throw new Exception("Feed object must be an instance of Generic_model to use this method");
    }

    /**
     * Returns unique id of the form instance
     *
     * @return Object
     */
    public function getInstanceCode()
    {
        if ($this->instance_code === null) {
            // TODO Rewrite POST parameter access
            $this->instance_code = isset($_POST['form_builder_instance_code']) ?
                $_POST['form_builder_instance_code'] : time() . '' . rand(10000, 99999);
        }
        return $this->instance_code;
    }

    /**
     * Sets ID used for representing working object using the feed object
     *
     * @param int $id
     * @return FormBuilder
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns ID used for representing working object
     *
     * @return int
     */
    public function getId()
    {
        if (!$this->id) {
            // TODO Rewrite POST parameter access
            $this->id = isset($_POST[self::POST_ID_FIELD_NAME]) ? $_POST[self::POST_ID_FIELD_NAME] : false;
        }

        return $this->id;
    }

    /**
     * The form is rendered for reading only when this set to TRUE
     *
     * @param bool $read_only
     * @return FormBuilder
     */
    public function setReadOnly($read_only = true)
    {
        $this->read_only = $read_only;
        return $this;
    }

    /**
     * Tells whether the form is read only
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->read_only;
    }

    /**
     * Sets form renderer
     *
     * @param FormRenderableInterface $renderer
     * @return FormBuilder
     */
    public function setRenderer(FormRenderableInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Returns form renderer
     *
     * @return FormRenderableInterface
     */
    public function getRenderer()
    {
        // Initializing default renderer
        if ($this->renderer == null) {
            $this->renderer = new DefaultFormRenderer();
        }
        return $this->renderer;
    }

    /**
     * Sets link that is used with the back button
     *
     * @param string $back_link
     * @return FormBuilder
     */
    public function setBackLink($back_link)
    {
        $this->back_link = $back_link;
        if (!preg_match('#^https?://#i', $this->back_link)) {
            $this->back_link = base_url() . $this->back_link;
        }

        return $this;
    }

    /**
     * Sets link that is used with the back button
     *
     * @return bool|string
     */
    public function getBackLink()
    {
        return $this->back_link;
    }

    /**
     * Sets to use simple session message on success
     *
     * @param bool
     */
    public function setUseSimpleSessionMessageOnSuccess($use = true)
    {
        $this->use_simple_session_message_on_save_success = $use;
    }

    /**
     * Returns whether to use simple session message on success
     *
     * @return bool
     */
    public function getUseSimpleSessionMessageOnSuccess()
    {
        return $this->use_simple_session_message_on_save_success;
    }

    /**
     * Returns the object carrying the DB data
     *
     * @return bool|object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Sets the object caring the DB data
     *
     * @param object $object
     * @return FormBuilder
     */
    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * Sets the callback of the specified type
     *
     * @param callable $callback
     * @param int $type
     * @param bool $check_callable
     * @return FormBuilder
     */
    public function setCallback($callback, $type, $check_callable = true)
    {
        if ($check_callable && !is_callable($callback)) {
            trigger_error('FormBuilder specified callback is not callable', E_USER_WARNING);
        }
        $this->callbacks[$type] = $callback;

        return $this;
    }

    /**
     * Sets the validation error message. The message must already be localized (not a key).
     *
     * @param string $message
     * @return FormBuilder
     */
    public function setValidationErrorMessage($message)
    {
        $this->validation_error_message = $message;
        return $this;
    }

    /**
     * Returns the validation error message
     *
     * @return string
     */
    public function getValidationErrorMessage()
    {
        return $this->validation_error_message;
    }

    /**
     * Tells whether the user will be redirected to back url on successful save
     *
     * @return bool
     */
    public function isRedirectOnSaveSuccess()
    {
        return $this->redirect_on_save_success;
    }

    /**
     * Enables disables redirect to back url on successful save
     *
     * @param bool $redirect_on_save_success
     * @return FormBuilder
     */
    public function setRedirectOnSaveSuccess($redirect_on_save_success)
    {
        $this->redirect_on_save_success = $redirect_on_save_success;
        return $this;
    }

    /**
     * Generates the form, the core method that handles both file upload, data update as well as rendering html output
     *
     * @return string
     */
    public function generate()
    {
        $this->generateSetNoCacheHeaders();

        // Default value
        $save_success = true;

        // Checking if the request was sent by a form builder generated form
        // Saving if POST form_builder_id is present
        if (isset($_POST[self::POST_ID_FIELD_NAME])) {
            $this->setId($_POST[self::POST_ID_FIELD_NAME]);
            unset($_POST[self::POST_ID_FIELD_NAME]);

            $validation_rules = $this->generateBuildUpValidationRules();

            // IMPORTANT!
            // Save array contains fields defined in form definition only!
            $save_array = $this->generateComputeSaveArrayFromPost();

            $isValid = $this->generateComputeIsValid($validation_rules);

            // Validating input
            if ($isValid) {
                $this->generateHandleFileUpload($save_array);

                // TODO Check if the file field is editable, if not, remove it from the save array

                $this->generateFixBooleanTypes($save_array);
                $this->callBeforeSaveCallbackIfNeccesary($save_array);

                try {
                    $save_success = $this->generateDoSave($save_array);
                } catch (Exception $e) {
                    $save_success = false;
                    $this->setValidationErrorMessage($e->getMessage());
                    Logger::error('Unable to save. Exception ' . get_class($e) . ' ' . $e->getMessage() . $e->getTraceAsString(),
                        'FORMBUILDER');
                }

                if ($save_success) {
                    if (!$this->getId()) { // There were no ID, try to determine it after the form is saved
                        $this->generateRefreshId($save_success);
                    }

                    if ($this->getId()) {
                        $this->generateHandleForeignKeyManyToManyUpdate($save_array);
                    }

                    if ($this->use_simple_session_message_on_save_success) {
                        $this->generateSetSuccessMessage();
                    }

                    $this->callAfterSaveCallbackIfNeccesary($save_array);
                    $this->generateHandleRedirectOnSuccess($this->input->post('apply') !== null);
                } else {
                    $this->generateSetErrorMessage();
                    $this->callOnSaveFailureCallbackIfNeccesary($save_array);
                }
            }

            // !
            // Regenerating the object from POST
            $this->generateRecreateObjectFromSaveArray($save_array);
        } else {
            // Executed for situation where not a POST save and no object found
            // This is called if there is no POST - reading object from the database
            if (!$this->object) {
                $this->generateDoReadObject();
                $this->generateDoAssignDefaultValuesForEmptyReadFields();
            }
        }

        $this->handleForeignKeys();
        $this->callBeforeRenderCallbackIfNeccesary();
        return $this->getRenderer()->render($this, $save_success);
    }

    /**
     * @return array
     */
    private function generateComputeSaveArrayFromPost()
    {
        $save_array = array();

        // Checking foreign keys for null values
        foreach ($this->fields as &$field) {
            $save_array[$field['field']] = $this->input->post($field['field']) !== null ?
                $this->input->post($field['field']) : '';

            if (!$save_array[$field['field']] && $field['foreign_key_accept_null']) {
                $save_array[$field['field']] = null;
            }
        }
        return $save_array;
    }

    /**
     * @param $is_ci3
     * @param $field
     * @return string
     */
    private function generateGetMappedFieldName($is_ci3, $field)
    {
        $field_name = $field['field'];
        // CI3 Validation has changed the way it validates arrays
        if ($is_ci3) {
            if (in_array($field['input_type'], array(FormBuilder::MULTIPLECHECKBOX, FormBuilder::MULTIPLESELECT))) {
                $field_name = $field['field'] . '[]';
            }
        }
        return $field_name;
    }

    private function generateSetSuccessMessage()
    {
        if (count($this->getUploadWarnings())) {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_NOTIFICATION)
                ->setRawMessage($this->lang->line('formbuilder_form_successfully_saved') . '<br><br><b>' . $this->lang->line('formbuilder_upload_warnings') . ':</b><br>' . implode('<br>', $this->getUploadWarnings()));
        } else {
            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS)
                ->setMessage('formbuilder_form_successfully_saved');
        }
    }

    /**
     * @param $save_array
     * @return mixed
     */
    private function generateRecreateObjectFromSaveArray($save_array)
    {
        // If there were no object, lets generate it from a dummy class to prevent useless errors
        if (!$this->object) {
            $this->object = new stdClass();
        }

        foreach ($this->fields as $field) {
            if (isset($save_array[$field['field']])) {
                $this->object->{$field['field']} = $save_array[$field['field']];
            } elseif (isset($_POST['form_builder_files'][$field['field']])) {
                $this->object->{$field['field']} = $_POST['form_builder_files'][$field['field']];
            } elseif ($field['input_type'] == FormBuilder::CHECKBOX || $field['input_type'] == FormBuilder::MULTIPLESELECT) {
                // Meaning no POST variable was set
                //FIXME Check validation of multiselect/multicheckbox when not selecting any values
                //echo $field['field'] . '=' . FALSE."<br>";
                $this->object->{$field['field']} = false;
            }
        }
    }

    /**
     * @param $field
     * @return bool
     */
    private function generateEnsureUploadDirectoryExits($field)
    {

        if (file_exists($field['upload_path']) && !is_dir($field['upload_path'])) {
            // If file exist, then checking if not a directory
            Logger::error('Upload path is a regular file and not a directory ' . $field['upload_path'], 'FORMBUILDER');
            return false;
        } elseif (!@mkdir($field['upload_path'])) {
            // If the file does not exist, attempt to create the path
            Logger::error('Unable to create directory ' . $field['upload_path'], 'FORMBUILDER');
            return false;
        }

        return true;
    }

    /**
     * @param $field_name
     * @return bool
     */
    private function doesUserWishToDeleteFile($field_name)
    {
        // $_POST['form_builder_files_remove'] field is a hidden field generated by the JavaScript
        if (isset($_POST['form_builder_files_remove'][$field_name]) && $_POST['form_builder_files_remove'][$field_name]) {
            return true;
        }
        return false;
    }

    /**
     * @param $field_name
     * @return array
     */
    private function generateComputeFileField($field_name)
    {
        if (isset($_POST['form_builder_files'][$field_name]) && strlen($_POST['form_builder_files'][$field_name]) > 0) {
            return $_POST['form_builder_files'][$field_name];
        }
        return null;
    }

    /**
     * @param $save_id_values
     * @param $field
     * @param $where_conditions
     */
    private function generateForeignKeyManyToManyDoInsert($save_id_values, $field, $where_conditions)
    {
        foreach ($save_id_values as $right_id) {
            // There is no value specified, skip the cycle
            if (!$right_id) {
                continue;
            }

            // TODO Consider situation then there is a non-db model

            // Saving new entry
            $this->db->set($field['foreign_key_junction_id_field_left'], $this->getId());
            // The following if allows to have multiple form fields having relations with the same foreign_key_junction_table
            if (count($where_conditions) > 0) {
                $this->db->set($where_conditions);
            }
            $this->db->set($field['foreign_key_junction_id_field_right'], $right_id)
                ->insert($field['foreign_key_junction_table']);
        }
    }

    /**
     * @param $field
     * @param $where_conditions
     */
    private function generateForeignKeyManyToManyDoDelete($field, $where_conditions)
    {
        $this->db->where($field['foreign_key_junction_id_field_left'], $this->getId());
        // The following if allows to have multiple form fields having relations with the same foreign_key_junction_table
        if (count($where_conditions) > 0) {
            $this->db->where($where_conditions);
        }
        $this->db->delete($field['foreign_key_junction_table']);
    }

    /**
     * @param $save_array
     * @return void
     */
    private function generateHandleForeignKeyManyToManyUpdate(&$save_array)
    {
        foreach ($this->fields as $field) {
            // As we are already looping, lets do some magic
            if ($field['foreign_key_table']
                && $field['foreign_key_junction_id_field_left']
                && $field['foreign_key_junction_id_field_right']
                && $field['foreign_key_junction_table']
                && $field['foreign_key_relationship_type'] == FormBuilder::FOREIGN_KEY_MANY_TO_MANY) {

                // Building where conditions based on the user input and the object ID
                // Since 0.2.4.3
                $where_conditions = (is_array($field['foreign_key_junction_where_conditions']) ?
                    $field['foreign_key_junction_where_conditions'] :
                    array());

                $this->generateForeignKeyManyToManyDoDelete($field, $where_conditions);


                if (is_array($save_array[$field['field']])) {
                    $this->generateForeignKeyManyToManyDoInsert($save_array[$field['field']], $field, $where_conditions);
                }
            }
        }
    }

    private function generateDoReadObject()
    {
        if (empty($this->object)) {
            $this->object = new stdClass();
        }

        if (isset($this->callbacks[self::CALLBACK_ON_READ])) {
            // There is on read callback, the object is retrieved using the callback function
            // The callback function must take the object (empty) by reference and must fill it
            call_user_func_array($this->callbacks[self::CALLBACK_ON_READ], array(&$this->object));
        } elseif ($this->feed_object && $this->id) {
            $this->object = $this->feed_object->getById($this->id);
        }
    }

    private function generateDoAssignDefaultValuesForEmptyReadFields()
    {
        // Assigning default values for fields that have no value storied in the database
        if ($this->object) {
            // For every field from the form definition
            foreach ($this->fields as &$field) {
                // If there is no value, lets try to get the implicit value

                if (!isset($this->object->{$field['field']})) {
                    $this->object->{$field['field']} = (isset($field['input_default_value']) && $field['input_default_value'] !== false ? $field['input_default_value'] : '');
                }
            }
        }
    }

    private function generateSetNoCacheHeaders()
    {
        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT')
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate')
            ->set_header('Cache-Control: post-check=0, pre-check=0')
            ->set_header('Pragma: no-cache');
    }

    /**
     * @param $save_array
     * @return void
     */
    private function generateHandleFileUpload(&$save_array)
    {
        if (count($this->file_upload_fields) == 0) {
            return;
        }

        foreach ($this->file_upload_fields as $upload_field_name) {
            if (!$this->generateEnsureUploadDirectoryExits($this->fields[$upload_field_name])) {
                continue;
            }

            // Reinitializing upload - necessary for consequent file uploads
            $this->upload->initialize($this->generateGetUploadConfig($upload_field_name));

            $upload = $this->upload->do_upload($upload_field_name);

            if (!$upload) {
                if ($this->hasSignificantUploadError()) {
                    $this->upload_warnings += $this->upload->error_msg;
                    // Do not overwrite database value in case of error
                    unset($save_array[$upload_field_name]);
                } else {
                    // No file was uploaded at all
                    $this->deleteFileIfUserFlagSelected($save_array, $upload_field_name);
                }
            } else {
                $this->deleteFileIfUserFlagSelected($save_array, $upload_field_name);

                // Calling a callback function after file upload
                // The callback function must take 3 parameters, $filename, $basepath and $data containing form data
                $data = $this->upload->data();
                $filename = $data['file_name'];
                if ($this->fields[$upload_field_name]['upload_complete_callback']) {
                    call_user_func_array($this->fields[$upload_field_name]['upload_complete_callback'],
                        array(&$filename,
                            &$this->fields[$upload_field_name]['upload_path'],
                            &$save_array,
                            $upload_field_name));
                }
                $save_array[$upload_field_name] = $filename;
            }
        }
    }

    /**
     * @param $upload_field_name
     * @return array
     */
    private function generateGetUploadConfig($upload_field_name)
    {
        return array(
            'upload_path' => $this->fields[$upload_field_name]['upload_path'],
            'allowed_types' => $this->fields[$upload_field_name]['upload_allowed_types'],
            'encrypt_name' => $this->fields[$upload_field_name]['upload_encrypt_name'],
        );
    }

    /**
     * @param $save_array
     * @return void
     */
    private function generateFixBooleanTypes(&$save_array)
    {
        // Fixing boolean field values, assigning TRUE or FALSE values
        foreach ($this->fields as &$field) {
            if ($field['input_type'] == FormBuilder::CHECKBOX) {
                $save_array[$field['field']] = (isset($save_array[$field['field']]) && $save_array[$field['field']] ? true : false);
            }
        }
    }

    /**
     * @param $field
     */
    private function generateConvertComasIntoDotsForNumericTypes($field)
    {
        if (strpos($field['validation_rules'], 'numeric') !== false
            && $this->input->post($field['field']) !== null) {
            $replaced = str_replace(',', '.', $this->input->post($field['field']));
            $_POST[$field['field']] = $replaced;
        }
    }

    /**
     * @param $is_ci3
     * @return array
     */
    private function generateBuildUpValidationRules()
    {
        // Computing rules, avoiding to pass a possibly huge array to the form validation method
        $is_ci3 = version_compare(CI_VERSION, '3', '>=');
        $validation_rules = array();

        foreach ($this->fields as $field) {
            // Protection against empty string, required by CI3
            $field_validation_rules = $field['validation_rules'] ? $field['validation_rules'] : false;

            if ($field_validation_rules) {
                $validation_rules[] = array(
                    'field' => $this->generateGetMappedFieldName($is_ci3, $field),
                    'label' => $field['label'],
                    'rules' => $field_validation_rules,
                );
            }

            $this->generateConvertComasIntoDotsForNumericTypes($field);
        }
        return $validation_rules;
    }

    /**
     * @param $save_array
     * @return mixed
     */
    private function generateDoSave(&$save_array)
    {
        $save_success = false;
        // For forms that are not saved in database directly // The order of these rows matter
        if (($use_callback = isset($this->callbacks[self::CALLBACK_ON_SAVE])) || !$this->feed_object) {
            // CALLBACK on save
            if ($use_callback) {
                $save_success = call_user_func_array($this->callbacks[self::CALLBACK_ON_SAVE], array(&$save_array));
            }
        } else {
            // No callback, use plain object save
            $save_success = $this->feed_object->saveById($this->id, $save_array);
        }
        return $save_success;
    }

    /**
     * @param $is_apply
     */
    private function generateHandleRedirectOnSuccess($is_apply)
    {
        // Prevent from redirecting on apply
        if (!$is_apply) {
            // For non-apply save, redirect to the back link
            if ($this->back_link && $this->redirect_on_save_success) {
                redirect($this->back_link);
                return;
            }
        }

        // END Prevent from redirecting on apply
        $this->output->set_header('X-XSS-Protection: 0');
    }

    /**
     * @param $field
     */
    private function generateForeignKeyFetchObjectValues($field)
    {
        // Resolving FOREIGN_KEY_MANY_TO_MANY relationship for a valid definition
        if ($field['foreign_key_relationship_type'] == FormBuilder::FOREIGN_KEY_MANY_TO_MANY
            && $field['foreign_key_junction_table']
            && $field['foreign_key_junction_id_field_right']
            && $field['foreign_key_junction_id_field_left']) {
            // This IF prevents from overwriting when the validation fails
            if (isset($this->object->{$field['field']}) && !$this->object->{$field['field']}) {
                // Building where conditions based on the user input and the object ID
                // Since 0.2.4.3 $where_conditions is read from foreign_key_junction_where_conditions instead of foreign_key_where_conditions
                // The elseif( is_array($field['foreign_key_where_conditions']) ) remains ONLY for backward compatibility
                if (is_array($field['foreign_key_junction_where_conditions'])) {
                    $where_conditions = $field['foreign_key_junction_where_conditions'];
                } elseif (is_array($field['foreign_key_where_conditions'])) {
                    $where_conditions = $field['foreign_key_where_conditions'];
                } else {
                    $where_conditions = array();
                }

                if ($this->getId()) {
                    $where_conditions += array($field['foreign_key_junction_id_field_left'] => $this->getId());
                }

                $this->object->{$field['field']} = $this
                    ->Generic_model->getAssocPairs($field['foreign_key_junction_id_field_right'],
                        $field['foreign_key_junction_id_field_right'], $field['foreign_key_junction_table'],
                        false,
                        false,
                        $where_conditions);
            }
        }
    }

    /**
     * @param $validation_rules
     * @return bool
     */
    private function generateComputeIsValid($validation_rules)
    {
        $isValid = true;
        // Setting validation rules if any of them exist
        if (count($validation_rules) > 0) {
            $this->form_validation->set_rules($validation_rules);
            $isValid = $this->form_validation->run() === true;
        }
        return $isValid;
    }

    /**
     * @param $field
     * @return mixed
     */
    private function generateForeignKeyFillFieldPossibleValues(&$field)
    {
        $should_fetch = false;
        if (!$field['input_is_editable']) {
            // is_array is required for multiple checkbox fields, etc - avoiding errors
            if (isset($this->object->{$field['field']})) {
                $possible_values = (is_array($this->object->{$field['field']})
                    ? $this->object->{$field['field']} : array($this->object->{$field['field']}));
            } else {
                $possible_values = array($field['input_default_value']);
            }
            $should_fetch = true;
        } elseif (!is_array($field['values'])) {
            $should_fetch = true;
            $possible_values = false;
        }


        if ($should_fetch) {
            $field['values'] = $this->Generic_model->getAssocPairs($field['foreign_key_field'],
                $field['foreign_key_label_field'],
                $field['foreign_key_table'],
                false,
                $possible_values,
                $field['foreign_key_where_conditions']);
        }


        // Adding an empty element for fields that accept null
        if ($field['foreign_key_accept_null']) {
            $field['values'] = array('' => '----') + $field['values'];
        }
    }

    /**
     * @param $save_success
     */
    private function generateRefreshId($save_success)
    {
        // Default ID comes from the database class
        $this->id = $this->db->insert_id();

        // Assigning ID when the success is a valid numeric value only
        if (is_numeric($save_success)) {
            $this->id = $save_success;
        }
    }

    /**
     * @return bool
     */
    private function hasSignificantUploadError()
    {
        return isset($this->upload->error_msg[0]) && $this->upload->error_msg[0] != $this->lang->line('upload_no_file_selected');
    }

    /**
     * @param $save_array
     * @param $upload_field_name
     * @return mixed
     */
    private function deleteFileIfUserFlagSelected(&$save_array, $upload_field_name)
    {
        $wish_to_delete_file = $this->doesUserWishToDeleteFile($upload_field_name);
        if ($wish_to_delete_file) {
            $file_name_to_delete = $this->generateComputeFileField($upload_field_name);
            $path_to_remove = $this->fields[$upload_field_name]['upload_path'] . $file_name_to_delete;
            if (file_exists($path_to_remove) && is_file($path_to_remove)) {
                if ($this->isOverwriteFilesOnUpload()) {
                    if (!@unlink($path_to_remove)) {
                        Logger::error('Unable to remove file ' . $path_to_remove, 'FORMBUILDER');
                    }
                }
            }
            $save_array[$upload_field_name] = '';
        } else {
            // Prevent from overwriting if the user simply does not wish to delete file
            unset($save_array[$upload_field_name]);
        }
    }

    private function generateSetErrorMessage()
    {
        if (empty($this->getValidationErrorMessage())) {
            $this->setValidationErrorMessage($this->lang->line('formbuilder_label_unable_to_save'));
        }

        // Sometimes there is no DB configured at all
        if (isset($this->db)) {
            $last_db_error = $this->db->error();

            $error_suffix = '';
            if ($last_db_error && isset($last_db_error['code']) && $last_db_error['code']) {
                $error_suffix = ' Last database error: ' . $last_db_error['code'] . ': ' . $last_db_error['message'];
            }

            $message = 'Unable to save the form. Model save method returned false.' . $error_suffix;
            Logger::error($message, 'FORMBUILDER');
        }
    }

    /**
     * @param array $save_array
     */
    private function callBeforeSaveCallbackIfNeccesary(&$save_array)
    {
        // CALLBACK before save
        if (isset($this->callbacks[self::CALLBACK_BEFORE_SAVE])) {
            call_user_func_array($this->callbacks[self::CALLBACK_BEFORE_SAVE], array(&$save_array));
        }
    }

    /**
     * @param array $save_array
     */
    private function callAfterSaveCallbackIfNeccesary(&$save_array)
    {
        if (isset($this->callbacks[self::CALLBACK_AFTER_SAVE])) {
            call_user_func_array($this->callbacks[self::CALLBACK_AFTER_SAVE], array(&$save_array));
        }
    }

    /**
     * @param array $save_array
     */
    private function callOnSaveFailureCallbackIfNeccesary(&$save_array)
    {
        if (isset($this->callbacks[self::CALLBACK_ON_SAVE_FAILURE])) {
            call_user_func_array($this->callbacks[self::CALLBACK_ON_SAVE_FAILURE], array(&$save_array));
        }
    }

    private function handleForeignKeys()
    {
        foreach ($this->fields as &$field) {
            if ($field['foreign_key_table']) {
                $this->generateForeignKeyFetchObjectValues($field);
                $this->generateForeignKeyFillFieldPossibleValues($field);
            }
        }
    }

    private function callBeforeRenderCallbackIfNeccesary()
    {
        if (isset($this->callbacks[self::CALLBACK_BEFORE_RENDER])) {
            call_user_func_array($this->callbacks[self::CALLBACK_BEFORE_RENDER], array(&$this->object));
        }
    }
}
