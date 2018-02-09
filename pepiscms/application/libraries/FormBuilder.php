<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
 * FormBuilder - utility for generating web forms
 *
 * Form builder handles form generation, validation and saving.
 * It can read data from any source that implements Entitable interface.
 *
 * @since 0.1.4
 *
 */
class FormBuilder
{

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
     * Multiple images, one can be checked
     * @var int
     * @deprecated as PepisCMS 0.2.4.1
     */
    const MULTIPLEIMAGES = 13;

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
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
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
        $this->is_overwrite_files_on_upload = TRUE;
        $this->form_submit_label = FALSE;
        $this->form_action = '';
        $this->is_submit_button_enabled = TRUE;
        $this->is_apply_button_enabled = FALSE;
        $this->use_simple_session_message_on_save_success = TRUE;
        $this->redirect_on_save_success = TRUE;
        $this->validation_error_message = FALSE;
        $this->back_link = FALSE;
        $this->object = FALSE;
        $this->read_only = FALSE;
        $this->instance_code = NULL;
        $this->id = FALSE;
        $this->file_upload_fields = array();
        $this->input_groups = array();
        $this->fields = array();
        $this->renderer = NULL;
        $this->feed_object = FALSE;
        $this->title = FALSE;
        $this->default_uploads_path = $this->default_upload_display_path = CI_Controller::get_instance()->config->item('uploads_path');
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
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     */
    public function setAction($action)
    {
        $this->form_action = $action;
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
     */
    public function setSubmitLabel($label)
    {
        $this->form_submit_label = $label;
    }

    /**
     * Returns submit label
     *
     * @return string
     */
    public function getSubmitLabel()
    {
        if (!$this->form_submit_label) {
            $this->form_submit_label = CI_Controller::get_instance()->lang->line('global_button_save_and_close');
        }

        return $this->form_submit_label;
    }

    /**
     * Enable or disable default submit, default is TRUE
     *
     * @param bool $is_submit_button_enabled
     */
    public function setSubmitButtonEnabled($is_submit_button_enabled = TRUE)
    {
        $this->is_submit_button_enabled = $is_submit_button_enabled;
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
     */
    public function setApplyButtonEnabled($is_apply_button_enabled = TRUE)
    {
        $this->is_apply_button_enabled = $is_apply_button_enabled;
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
        return (CI_Controller::get_instance()->input->get('apply', NULL) !== NULL && CI_Controller::get_instance()->input->get('apply') == '');
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
        return (isset($this->fields[$field_name]) ? $this->fields[$field_name] : FALSE);
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
     * @return bool
     */
    public function addField($field, $label = FALSE, $type = FALSE, $default_value = FALSE, $rules = FALSE, $values = FALSE)
    {
        // If the first element is array, then setting field by definition
        if (is_array($field)) {
            return $this->addFieldByDefinition($field);
        }

        if ($type === FALSE) {
            $type = FormBuilder::TEXTFIELD;
        }
        if ($rules === FALSE) {
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
        $defaults['field'] = FALSE; // Field name
        $defaults['label'] = FALSE; // Field label
        $defaults['description'] = FALSE; // Field description
        // Display options
        $defaults['show_in_form'] = TRUE; // Display in form?
        $defaults['show_in_grid'] = TRUE; // Display in grid?
        // Foreign key
        $defaults['foreign_key_relationship_type'] = FormBuilder::FOREIGN_KEY_ONE_TO_MANY;
        $defaults['foreign_key_table'] = FALSE;
        $defaults['foreign_key_field'] = 'id';
        $defaults['foreign_key_label_field'] = 'id';
        $defaults['foreign_key_accept_null'] = FALSE;
        $defaults['foreign_key_where_conditions'] = FALSE;

        $defaults['foreign_key_junction_table'] = FALSE;
        $defaults['foreign_key_junction_id_field_left'] = FALSE;
        $defaults['foreign_key_junction_id_field_right'] = FALSE;
        $defaults['foreign_key_junction_where_conditions'] = FALSE;

        //
        // Input specific
        //
        $defaults['input_type'] = FormBuilder::TEXTFIELD; // Input type, see FormBuilder constants
        $defaults['input_default_value'] = FALSE; // Default value for field
        $defaults['values'] = FALSE; // Values for to select among them, must be an associative array
        $defaults['validation_rules'] = 'required'; // Validation rules
        $defaults['input_is_editable'] = TRUE;
        $defaults['input_group'] = FALSE;
        $defaults['input_css_class'] = FALSE;
        $defaults['options'] = array();

        // File upload
        $defaults['upload_complete_callback'] = FALSE;
        $defaults['upload_path'] = $this->default_uploads_path;
        $defaults['upload_display_path'] = $this->default_upload_display_path;
        $defaults['upload_allowed_types'] = FALSE;
        $defaults['upload_encrypt_name'] = FALSE;

        //
        // Grid specific
        //
        $defaults['grid_formating_callback'] = FALSE;
        $defaults['grid_is_orderable'] = TRUE;
        $defaults['grid_css_class'] = FALSE;
        $defaults['filter_type'] = FALSE;
        $defaults['filter_values'] = FALSE;
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
    public function setOverwriteFilesOnUpload($is_overwrite_files_on_upload = TRUE)
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
     * @return bool
     */
    public function setDefinition($fields)
    {
        // Resetting values
        $this->fields = array();
        $this->input_groups = array();

        foreach ($fields as $key => &$field) {
            // Make it work with associative
            if ($key && (!isset($field['field']) || $field['field'] === FALSE)) {
                $field['field'] = $key;
            }
            $this->addFieldByDefinition($field);
        }

        return TRUE;
    }

    /**
     * Sets field by array definition
     * @param array $field
     * @return bool
     */
    public function addFieldByDefinition($field)
    {
        if (!isset($field['field']) || !$field['field']) {
            return FALSE;
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
            return FALSE;
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
            $this->input_groups[$defaults['input_group']] = array('label' => ucfirst(str_replace('_', ' ', $defaults['input_group'])), 'description' => FALSE, 'fields' => array());
        }
        $this->input_groups[$defaults['input_group']]['fields'][] = $defaults['field'];


        if ($defaults['input_type'] == FormBuilder::IMAGE || $defaults['input_type'] == FormBuilder::FILE) {
            $this->file_upload_fields[$defaults['field']] = $defaults['field'];
            $defaults['validation_rules'] = '';
            if ($defaults['upload_allowed_types'] === FALSE && $defaults['input_type'] == FormBuilder::IMAGE) {
                $defaults['upload_allowed_types'] = 'jpg|jpeg|gif|png|bmp';
            }
        }

        $defaults['label'] = $defaults['label'] !== FALSE ? $defaults['label'] : ucfirst(str_replace('_', ' ', $defaults['field']));
        $this->fields[$defaults['field']] = $defaults;

        return true;
    }

    /**
     * Adds an image field
     *
     * @param $field
     * @param bool $label
     * @param bool $upload_path
     * @param bool $upload_display_path
     * @param bool $upload_complete_callback
     * @return bool
     */
    public function addImageField($field, $label = FALSE, $upload_path = FALSE, $upload_display_path = FALSE, $upload_complete_callback = FALSE)
    {
        trigger_error('FormBuilder::addImageField() is deprecated as PepisCMS 0.2.4.1', E_USER_DEPRECATED);

        $field_info = array(
            'field' => $field,
            'label' => $label ? $label : ucfirst(str_replace('_', ' ', $field)),
            'input_type' => FormBuilder::IMAGE,
            'upload_path' => ($upload_path ? $upload_path : $this->default_uploads_path),
            'upload_display_path' => ($upload_display_path ? $upload_display_path : $this->default_upload_display_path),
            'upload_complete_callback' => $upload_complete_callback,
            'upload_allowed_types' => 'jpg|jpeg|gif|png|bmp',
            'upload_encrypt_name' => FALSE,
            'validation_rules' => ''
        );
        return $this->addFieldByDefinition($field_info);
    }

    /**
     * Adds an file field
     *
     * @param $field
     * @param bool $label
     * @param bool $upload_path
     * @param string $upload_allowed_types
     * @param bool $upload_complete_callback
     * @return bool
     */
    public function addFileField($field, $label = FALSE, $upload_path = FALSE, $upload_allowed_types = '', $upload_complete_callback = FALSE)
    {
        trigger_error('FormBuilder::addFileField() is deprecated as PepisCMS 0.2.4.1', E_USER_DEPRECATED);

        $field_info = array(
            'field' => $field,
            'label' => $label ? $label : ucfirst(str_replace('_', ' ', $field)),
            'input_type' => FormBuilder::FILE,
            'upload_path' => ($upload_path ? $upload_path : $this->default_uploads_path),
            'upload_complete_callback' => $upload_complete_callback,
            'upload_allowed_types' => $upload_allowed_types,
            'upload_encrypt_name' => FALSE,
            'validation_rules' => '',
        );
        return $this->addFieldByDefinition($field_info);
    }

    /**
     * Sets the feed object implementing feedable interface
     *
     * @param EntitableInterface $feed_object
     * @return bool
     */
    public function setFeedObject(EntitableInterface $feed_object)
    {
        $this->feed_object = $feed_object;
        return TRUE;
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
     * @return bool
     */
    public function setTable($tableName, $acceptedPostFields = array(), $idFieldName = FALSE)
    {
        // Initializes a cloned Generic Model with a specified table
        $feed_object = clone CI_Controller::get_instance()->Generic_model;
        $feed_object->setTable($tableName);

        // Specify id field name, otherwise use the default value from Generic Model
        if ($idFieldName) {
            $feed_object->setIdFieldName($idFieldName);
        }

        $feed_object->setAcceptedPostFields($acceptedPostFields);
        $this->setFeedObject($feed_object);
        return TRUE;
    }

    /**
     * Adds accepted post field, useful in callbacks
     *
     * @param string $field
     * @return bool
     */
    public function addAcceptedPostField($field)
    {
        if ($this->feed_object instanceof Generic_model) {
            $this->feed_object->addAcceptedPostField($field);
            return TRUE;
        }
        return FALSE;
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
            $this->instance_code = isset($_POST['form_builder_instance_code']) ? $_POST['form_builder_instance_code'] : time() . '' . rand(10000, 99999);
        }
        return $this->instance_code;
    }

    /**
     * Sets ID used for representing working object using the feed object
     *
     * @param int $id
     * @return bool
     */
    public function setId($id)
    {
        if (!$id && $id !== 0) {
            return FALSE;
        }

        $this->id = $id;
        return TRUE;
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
            $this->id = isset($_POST['form_builder_id']) ? $_POST['form_builder_id'] : FALSE;
        }

        return $this->id;
    }

    /**
     * The form is rendered for reading only when this set to TRUE
     *
     * @param bool $read_only
     * @return bool
     */
    public function setReadOnly($read_only = TRUE)
    {
        $this->read_only = $read_only;
        return TRUE;
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
     * @param FormRenderable $renderer
     * @return bool
     */
    public function setRenderer(FormRenderable $renderer)
    {
        $this->renderer = $renderer;
        return TRUE;
    }

    /**
     * Returns form renderer
     *
     * @return FormRenderable
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
     */
    public function setBackLink($back_link)
    {
        $this->back_link = $back_link;
        if (!preg_match('#^https?://#i', $this->back_link)) {
            $this->back_link = base_url() . $this->back_link;
        }
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
    public function setUseSimpleSessionMessageOnSuccess($use = TRUE)
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
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * Sets the callback of the specified type
     *
     * @param callable $callback
     * @param int $type
     * @param bool $check_callable
     */
    public function setCallback($callback, $type, $check_callable = TRUE)
    {
        if ($check_callable && !is_callable($callback)) {
            trigger_error('FormBuilder specified callback is not callable', E_USER_WARNING);
        }
        $this->callbacks[$type] = $callback;
    }

    /**
     * Sets the validation error message
     *
     * @param string $message
     */
    public function setValidationErrorMessage($message)
    {
        $this->validation_error_message = $message;
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
     * Generates the form, the core method that handles both file upload, data update as well as rendering html output
     *
     * @return string
     */
    public function generate()
    {
        // Loading all the necessary libraries and language
        CI_Controller::get_instance()->load->language('formbuilder');
        CI_Controller::get_instance()->load->library('form_validation');

        // Sending no-cache header
        CI_Controller::get_instance()->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        CI_Controller::get_instance()->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        CI_Controller::get_instance()->output->set_header('Cache-Control: post-check=0, pre-check=0');
        CI_Controller::get_instance()->output->set_header('Pragma: no-cache');

        // Default value
        $save_success = TRUE;

        // Computing rules, avoiding to pass a possibly huge array to the form validation method
        $is_ci3 = version_compare(CI_VERSION, '3', '>=');

        $validation_rules = array();

        // When there are no validation rules, it makes no sense to test the form if valid
        $validation_has_any_rules = FALSE;

        // For every defined field
        foreach ($this->fields as &$field) {
            $field_name = $field['field'];

            // CI3 Validation has changed the way it validates arrays
            if ($is_ci3) {
                if (in_array($field['input_type'], array(FormBuilder::MULTIPLECHECKBOX, FormBuilder::MULTIPLESELECT))) {
                    $field_name = $field['field'] . '[]';
                }
            }

            // Protection against empty string, required by CI3
            $field_validation_rules = $field['validation_rules'] ? $field['validation_rules'] : FALSE;

            // Reducing
            $validation_rules[] = array(
                'field' => $field_name,
                'label' => $field['label'],
                'rules' => $field_validation_rules,
            );

            // We need to save a variable indicating there are validation rules
            if ($field_validation_rules) {
                $validation_has_any_rules = TRUE;
            }

            // Filtering numeric input before validation and replacing coma with a dot
            if (strpos($field['validation_rules'], 'numeric') !== FALSE && isset($_POST[$field['field']])) {
                $_POST[$field['field']] = str_replace(',', '.', $_POST[$field['field']]);
            }
        }

        // Setting validation rules if any of them exist
        if ($validation_has_any_rules) {
            CI_Controller::get_instance()->form_validation->set_rules($validation_rules);
        }


        // Checking if the request was sent by a form builder generated form
        // Saving if POST form_builder_id is present
        if (isset($_POST['form_builder_id'])) {
            $this->setId($_POST['form_builder_id']);
            unset($_POST['form_builder_id']);

            // IMPORTANT!
            // Save array contains fields defined in form definition only!
            $save_array = array();

            // Checking foreign keys for null values
            foreach ($this->fields as &$field) {
                $save_array[$field['field']] = isset($_POST[$field['field']]) ? CI_Controller::get_instance()->input->post($field['field']) : '';

                if (!$save_array[$field['field']] && $field['foreign_key_accept_null']) {
                    $save_array[$field['field']] = NULL;
                }
            }

            // Validating input
            if (!$validation_has_any_rules || CI_Controller::get_instance()->form_validation->run() === TRUE) {
                // Saving
                // Checking if there are any file upload  fields to handle
                if (count($this->file_upload_fields) > 0) {
                    CI_Controller::get_instance()->load->library('upload');

                    foreach ($this->file_upload_fields as &$field) {
                        // If file exist, then checking if not a directory
                        if (file_exists($this->fields[$field]['upload_path'])) {
                            // Checking if something else than a directory
                            if (!is_dir($this->fields[$field]['upload_path'])) {
                                Logger::error('Upload path is a regular file and not a directory ' . $this->fields[$field]['upload_path'], 'FORMBUILDER');
                                continue;
                            }
                        } else {
                            // If the file does not exist, attempt to create the path
                            if (!@mkdir($this->fields[$field]['upload_path'])) {
                                Logger::error('Unable to create directory ' . $this->fields[$field]['upload_path'], 'FORMBUILDER');
                                continue;
                            }
                        }


                        // Messy code, do not touch
                        // $fileIndexesToRemove start with 1! not with 0
                        $file_indexes_to_remove = array();

                        // Checking if a given field was marked as to be deleted
                        // $_POST['form_builder_files_remove'] field is a hidden field generated by the JavaScript
                        if (isset($_POST['form_builder_files_remove'][$field])) {
                            if (!is_array($_POST['form_builder_files_remove'][$field])) {
                                $_POST['form_builder_files_remove'][$field] = array($_POST['form_builder_files_remove'][$field]);
                            }

                            foreach ($_POST['form_builder_files_remove'][$field] as $i) {
                                if ($i > 0) {
                                    $file_indexes_to_remove[] = $i;
                                }
                            }
                        }

                        // $form_builder_files must be an array
                        // TODO Remove multiple file
                        $form_builder_field_files = array();
                        if (isset($_POST['form_builder_files'][$field])) {
                            // TODO Standardize array file fields
                            if (!is_array($_POST['form_builder_files'][$field]) && strlen($_POST['form_builder_files'][$field]) > 0) {
                                // TODO Standardize array file fields
                                $form_builder_field_files = array($_POST['form_builder_files'][$field]);
                            }
                        }

                        //die(var_dump($fbFiles));
                        //END Messy code, do not touch

                        // TODO $this->fields[$field] might be dangerous when no keys are specified (field name specified as an element of the array)
                        $config['upload_path'] = $this->fields[$field]['upload_path'];
                        $config['allowed_types'] = $this->fields[$field]['upload_allowed_types'];
                        $config['encrypt_name'] = $this->fields[$field]['upload_encrypt_name'];

                        // Reinitializing upload - necessary for consequent file uploads
                        CI_Controller::get_instance()->upload->initialize($config);

                        // Needed for resetting field value, especially for the case when we do not overwrite the previous files
                        $is_any_file_removed = false;

                        $path_to_remove = false;
                        // Removing user marked files
                        // TODO Check if array is needed
                        foreach ($file_indexes_to_remove as $file_index_to_remove) {
                            $i = $file_index_to_remove - 1;
                            $path_to_remove = $this->fields[$field]['upload_path'] . $form_builder_field_files[$i];
                            // Checking if the file exists and really a file
                            if (file_exists($path_to_remove) && is_file($path_to_remove)) {
                                // Delete the file only if the overwrite option is true
                                if ($this->isOverwriteFilesOnUpload()) {
                                    // Delete file that was marked as DELETED
                                    if (!@unlink($path_to_remove)) {
                                        Logger::error('Unable to remove file ' . $path_to_remove, 'FORMBUILDER');
                                    }
                                }
                            }
                            $is_any_file_removed = TRUE;

                            // Removing the index no matter the file exists or no
                            unset($form_builder_field_files[$i]);
                        }


                        $upload = CI_Controller::get_instance()->upload->do_upload($field);

                        if (!$upload) {
                            // Logging all errors except upload_no_file_selected
                            if (isset(CI_Controller::get_instance()->upload->error_msg[0]) && CI_Controller::get_instance()->upload->error_msg[0] != CI_Controller::get_instance()->lang->line('upload_no_file_selected')) {
                                // Logging error
                                $this->upload_warnings += CI_Controller::get_instance()->upload->error_msg;
                            }

                            // On failure removing field, so that the original image is kept
                            if (!$is_any_file_removed) {
                                unset($save_array[$field]);
                            }
                        } else {
                            $data = CI_Controller::get_instance()->upload->data();
                            $filename = $data['file_name'];

                            // Removing the previous image/file - overwriting
                            if (($this->fields[$field]['input_type'] == FormBuilder::IMAGE || $this->fields[$field]['input_type'] == FormBuilder::FILE) && isset($form_builder_field_files[0])) {
                                $file_to_remove = $this->fields[$field]['upload_path'] . $form_builder_field_files[0];
                                if (file_exists($file_to_remove) && is_file($file_to_remove)) {
                                    // Delete the file only if the overwrite option is true
                                    if ($this->isOverwriteFilesOnUpload() && $path_to_remove) {
                                        // Delete file that was just OVERWRITTEN
                                        if (!@unlink($path_to_remove)) {
                                            Logger::error('Unable to remove file ' . $path_to_remove, 'FORMBUILDER');
                                        }
                                    }
                                }
                                unset($form_builder_field_files[0]); // In case the new file has different name
                            }


                            // Calling a callback function after file upload
                            // The callback function must take 3 parameters, $filename, $basepath and $data containing form data
                            if ($this->fields[$field]['upload_complete_callback']) {
                                call_user_func_array($this->fields[$field]['upload_complete_callback'], array(&$filename, &$this->fields[$field]['upload_path'], &$save_array, $field));
                            }
                            $form_builder_field_files[] = $filename;

                        }// End uploading

                        // Unsetting files that were not really uploaded, elements that are empty strings
                        foreach ($form_builder_field_files as &$form_builder_file) {
                            // Checking if the file is really set
                            if (strlen(trim($form_builder_file)) == 0) {
                                $form_builder_file = NULL;
                                unset($form_builder_file);
                            }
                        }


                        $count_form_builder_field_files = count($form_builder_field_files);

                        // If there are no field files - reset the field value
                        if ($count_form_builder_field_files == 0) {
                            if ($is_any_file_removed) {
                                $save_array[$field] = '';
                            }
                        } // If there is a single file, add only the given file
                        elseif ($count_form_builder_field_files == 1 && isset($form_builder_field_files[0])) {
                            $save_array[$field] = $form_builder_field_files[0];
                        } // Otherwise glue the files
                        else {
                            // TODO Remove multiple files
                            $save_array[$field] = implode(';', $form_builder_field_files);
                        }
                    }
                } // END Checking if there are any file upload  fields to handle


                // TODO Check if the file field is editable, if not, remove it from the save array


                // Fixing boolean field values, assigning TRUE or FALSE values
                foreach ($this->fields as &$field) {
                    if ($field['input_type'] == FormBuilder::CHECKBOX) {
                        $save_array[$field['field']] = (isset($save_array[$field['field']]) && $save_array[$field['field']] ? TRUE : FALSE);
                    }
                }

                // CALLBACK before save
                if (isset($this->callbacks[self::CALLBACK_BEFORE_SAVE])) {
                    call_user_func_array($this->callbacks[self::CALLBACK_BEFORE_SAVE], array(&$save_array));
                }

                try {
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
                } catch (Exception $e) {
                    $save_success = FALSE;
                    $this->setValidationErrorMessage($e->getMessage());
                    Logger::error('Unable to save. Exception ' . get_class($e) . ' ' . $e->getTraceAsString(), 'FORMBUILDER');

                }


                // Saving user data
                if ($save_success) {
                    // TODO Rewrite POST parameter access
                    $is_apply = isset($_POST['apply']);

                    // There were no ID, try to determine it after the form is saved
                    if (!$this->id) // $is_apply
                    {
                        // Default ID comes from the database class
                        // TODO Consider situation then there is a non-db model
                        $this->id = CI_Controller::get_instance()->db->insert_id();

                        // Assigning ID when the success is a valid numeric value only
                        if (is_numeric($save_success)) {
                            $this->id = $save_success;
                        }
                    }

                    // Saving many to many relationships
                    if ($this->getId()) {
                        foreach ($this->fields as $key => $aField) {
                            // As we are already looping, lets do some magic
                            if ($aField['foreign_key_table'] && $aField['foreign_key_junction_id_field_left'] && $aField['foreign_key_junction_id_field_right'] && $aField['foreign_key_junction_table'] && $aField['foreign_key_relationship_type'] == FormBuilder::FOREIGN_KEY_MANY_TO_MANY) {
                                // Building where conditions based on the user input and the object ID
                                // Since 0.2.4.3
                                $where_conditions = (is_array($aField['foreign_key_junction_where_conditions']) ? $aField['foreign_key_junction_where_conditions'] : array());

                                // First removing old entries
                                CI_Controller::get_instance()->db->where($aField['foreign_key_junction_id_field_left'], $this->getId());
                                // The following if allows to have multiple form fields having relations with the same foreign_key_junction_table
                                if (count($where_conditions) > 0) {
                                    CI_Controller::get_instance()->db->where($where_conditions);
                                }
                                CI_Controller::get_instance()->db->delete($aField['foreign_key_junction_table']);

                                if (is_array($save_array[$aField['field']])) {
                                    foreach ($save_array[$aField['field']] as $right_id) {
                                        // There is no value specified, skip the cycle
                                        if (!$right_id) {
                                            continue;
                                        }

                                        // TODO Consider situation then there is a non-db model

                                        // Saving new entry
                                        CI_Controller::get_instance()->db->set($aField['foreign_key_junction_id_field_left'], $this->getId());
                                        // The following if allows to have multiple form fields having relations with the same foreign_key_junction_table
                                        if (count($where_conditions) > 0) {
                                            CI_Controller::get_instance()->db->set($where_conditions);
                                        }
                                        CI_Controller::get_instance()->db->set($aField['foreign_key_junction_id_field_right'], $right_id)
                                            ->insert($aField['foreign_key_junction_table']);
                                    }
                                }
                            }
                        }
                    } // END Saving many to many relationships

                    // Set simple session message on success, if enabled
                    if ($this->use_simple_session_message_on_save_success) {
                        // TODO Try to abstract it out from the FormBuilder class
                        CI_Controller::get_instance()->load->library('SimpleSessionMessage');

                        if (count($this->getUploadWarnings())) {
                            CI_Controller::get_instance()->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_NOTIFICATION);
                            CI_Controller::get_instance()->simplesessionmessage->setRawMessage(CI_Controller::get_instance()->lang->line('formbuilder_form_successfully_saved') . '<br><br><b>' . CI_Controller::get_instance()->lang->line('formbuilder_upload_warnings') . ':</b><br>' . implode('<br>', $this->getUploadWarnings()));
                        } else {
                            CI_Controller::get_instance()->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
                            CI_Controller::get_instance()->simplesessionmessage->setMessage('formbuilder_form_successfully_saved');
                        }
                    }

                    // CALLBACK
                    if (isset($this->callbacks[self::CALLBACK_AFTER_SAVE])) {
                        call_user_func_array($this->callbacks[self::CALLBACK_AFTER_SAVE], array(&$save_array));
                    }

                    // Prevent from redirecting on apply
                    if (!$is_apply) {
                        // For non-apply save, redirect to the back link
                        if ($this->back_link && $this->redirect_on_save_success) {
                            redirect($this->back_link);
                        }

                    } else {
                        // END Prevent from redirecting on apply
                        CI_Controller::get_instance()->output->set_header('X-XSS-Protection: 0');
                    }
                } // Otherwise for save failure
                else {
                    // CALLBACK on failure
                    if (isset($this->callbacks[self::CALLBACK_ON_SAVE_FAILURE])) {
                        call_user_func_array($this->callbacks[self::CALLBACK_ON_SAVE_FAILURE], array(&$save_array));
                    }
                }
            }

            // If there were no object, lets generate it from a dummy class to prevent useless errors
            if (!$this->object) {
                $this->object = new stdClass();
            }

            // !
            // Regenerating the object from POST
            foreach ($this->fields as &$field) {
                if (isset($save_array[$field['field']])) {
                    $this->object->$field['field'] = $save_array[$field['field']];
                } elseif (isset($_POST['form_builder_files'][$field['field']])) {
                    $this->object->$field['field'] = $_POST['form_builder_files'][$field['field']];
                } elseif ($field['input_type'] == FormBuilder::CHECKBOX || $field['input_type'] == FormBuilder::MULTIPLESELECT) {
                    // Meaning no POST variable was set
                    //FIXME Check validation of multiselect/multicheckbox when not selecting any values
                    //echo $field['field'] . '=' . FALSE."<br>";
                    $this->object->$field['field'] = FALSE;
                }
            }
        } // END POST, END OF FORM SAVE


        else {
            // Executed for situation where not a POST save and no object found
            // This is called if there is no POST - reading object from the database
            if (!$this->object) {
                // CALLBACK
                if (isset($this->callbacks[self::CALLBACK_ON_READ])) {
                    // There is on read callback, the object is retrieved using the callback function
                    // The callback function must take the object (empty) by reference and must fill it
                    call_user_func_array($this->callbacks[self::CALLBACK_ON_READ], array(&$this->object));
                } elseif ($this->feed_object && $this->id) {
                    // No callback, read the object from the feed
                    $this->object = $this->feed_object->getById($this->id);
                }

                // Assigning default values for fields that have no value storied in the database
                if ($this->object) {
                    // For every field from the form definition
                    foreach ($this->fields as &$field) {
                        // If there is no value, lets try to get the implicit value
                        if (!isset($this->object->$field['field'])) {
                            $this->object->$field['field'] = (isset($field['input_default_value']) && $field['input_default_value'] !== FALSE ? $field['input_default_value'] : '');
                        }
                    }
                } // END is object
            }
        }

        // For every field from the form definition
        foreach ($this->fields as &$field) {
            // As we are already looping, lets do some magic
            if ($field['foreign_key_table']) {
                // Resolving FOREIGN_KEY_MANY_TO_MANY relationship for a valid definition
                if ($field['foreign_key_relationship_type'] == FormBuilder::FOREIGN_KEY_MANY_TO_MANY && $field['foreign_key_junction_table'] && $field['foreign_key_junction_id_field_right'] && $field['foreign_key_junction_id_field_left']) {
                    // This IF prevents from overwriting when the validation fails
                    if (isset($this->object->$field['field']) && !$this->object->$field['field']) {
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

                        $this->object->$field['field'] = CI_Controller::get_instance()->Generic_model->getAssocPairs($field['foreign_key_junction_id_field_right'], $field['foreign_key_junction_id_field_right'], $field['foreign_key_junction_table'], FALSE, FALSE, $where_conditions);
                    }
                }

                if (!$field['input_is_editable']) {
                    // is_array is required for multiple checkbox fields, etc
                    $possible_values = isset($this->object->$field['field']) ? (is_array($this->object->$field['field']) ? $this->object->$field['field'] : array($this->object->$field['field'])) : array($field['input_default_value']); // Avoiding error
                    $field['values'] = CI_Controller::get_instance()->Generic_model->getAssocPairs($field['foreign_key_field'], $field['foreign_key_label_field'], $field['foreign_key_table'], FALSE, $possible_values, $field['foreign_key_where_conditions']);
                } elseif (!is_array($field['values'])) {
                    $field['values'] = CI_Controller::get_instance()->Generic_model->getAssocPairs($field['foreign_key_field'], $field['foreign_key_label_field'], $field['foreign_key_table'], FALSE, FALSE, $field['foreign_key_where_conditions']);
                }

                // Adding an empty element for fields that accept null
                if ($field['foreign_key_accept_null']) {
                    $field['values'] = array('' => '----') + $field['values'];
                }
            }
        }

        // CALLBACK
        if (isset($this->callbacks[self::CALLBACK_BEFORE_RENDER])) {
            call_user_func_array($this->callbacks[self::CALLBACK_BEFORE_RENDER], array(&$this->object));
        }

        // Returns rendered HTML
        return $this->getRenderer()->render($this, $save_success);
    }

}

/**
 * Form renderable interface that specifies methods that must be implemented by
 * any form template renderer
 *
 * Usually you don't use this interface directly but extend DefaultFormRenderer
 */
interface FormRenderable
{

    /**
     * Renders form out of the formbuilder
     *
     * @param FormBuilder $formbuilder
     * @param $success
     * @return string
     */
    public function render($formbuilder, $success);

    /**
     * Sets error delimiters
     *
     * @param string $prefix
     * @param string $suffix
     *
     */
    public function setErrorDelimiters($prefix, $suffix);

    /**
     * @return FormBuilder
     */
    public function getFormBuilder();
}

/**
 * Default renderer for FormBuilder
 *
 * Default renderer is often extended by custom renderers
 *
 * @since 0.1.5
 */
class DefaultFormRenderer implements FormRenderable
{

    protected $is_js_included = FALSE;
    protected $validation_message_prefix = FALSE;
    protected $validation_message_suffix = FALSE;
    protected $template_absolute_path = FALSE;
    /** @var FormBuilder */
    protected $formbuilder;
    protected $success;

    /**
     * DefaultFormRenderer constructor.
     * @param array|bool $template_absolute_path
     */
    public function __construct($template_absolute_path = FALSE)
    {
        if (!$template_absolute_path) {
            $template_absolute_path = APPPATH . 'views/templates/formbuilder_default.php';
        }

        $this->template_absolute_path = $template_absolute_path;

        $this->validation_message_prefix = get_warning_begin();
        $this->validation_message_suffix = get_warning_end();
    }

    /**
     * Renders the form and returns HTML code
     *
     * @param FormBuilder $formbuilder
     * @param bool $success
     * @return string
     */
    public function render($formbuilder, $success)
    {
        $this->formbuilder = $formbuilder; // Required!
        $this->success = $success;

        if (!$this->template_absolute_path || !file_exists($this->template_absolute_path)) {
            // TODO throw exception instead of die
            trigger_error('Specified template file ' . $this->template_absolute_path . ' does not exit. Unable to render FormBuilder template.', E_USER_ERROR);
            return '';
        }

        return $this->renderTemplate();
    }

    /**
     * Returns the array containing input groups
     *
     * @return array
     */
    public function getInputGroups()
    {
        return $this->formbuilder->getInputGroups();
    }

    /**
     * Sets the absolute path of the form's template
     *
     * @param string $template_absolute_path
     */
    public function setTemplatePath($template_absolute_path)
    {
        $this->template_absolute_path = $template_absolute_path;
    }

    /**
     * Returns the absolute path of the form's template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->template_absolute_path;
    }

    /**
     * Renders form buttons
     *
     * @return string
     */
    public function getFormButtons()
    {
        $output = '<div class="buttons">' . "\n";
        if ($this->formbuilder->getBackLink()) {
            $output .= button_cancel($this->formbuilder->getBackLink());
        }

        if (!$this->formbuilder->isReadOnly()) {
            if ($this->formbuilder->isApplyButtonEnabled() && $this->formbuilder->getId()) {
                $output .= button_apply() . "\n";
            }

            if ($this->formbuilder->isSubmitButtonEnabled()) {
                $output .= button_save('', FALSE, $this->formbuilder->getSubmitLabel()) . "\n";
            }
        }
        $output .= '</div>' . "\n";

        return $output;
    }

    /**
     * Returns HTML for opening form
     *
     * @return string
     */
    public function openForm()
    {
        $output = '<form method="POST" action="' . $this->formbuilder->getAction() . '" enctype="multipart/form-data" accept-charset="UTF-8" class="validable' . ($this->formbuilder->isReadOnly() ? ' readonly' : '') . '">' . "\n";
        $output .= '<input type="hidden" name="form_builder_id" value="' . $this->formbuilder->getId() . '" />' . "\n";
        $output .= '<input type="hidden" name="form_builder_instance_code" value="' . $this->formbuilder->getInstanceCode() . '" />' . "\n";
        return $output;
    }

    /**
     * Returns HTML for closing form
     *
     * @return string
     */
    public function closeForm()
    {
        return '</form>';
    }

    /**
     * Tells whether form contains a field of specified name
     *
     * @param string $field_name
     * @return bool
     */
    public function hasField($field_name)
    {
        $field = $this->formbuilder->getField($field_name);
        if (!$field) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Returns label for given at field. By default it returns HTML code
     *
     * @param string $field_name
     * @param bool $html
     * @return string
     */
    public function getFieldLabel($field_name, $html = true)
    {
        $field = $this->formbuilder->getField($field_name);
        if (!$field) {
            return FALSE;
        }

        if ($html) {
            $extra_css_classes = ($field['input_is_editable'] && strpos($field['validation_rules'], 'required') !== FALSE ? ' required' : '');
            return '<label for="' . $field_name . '"' . ($extra_css_classes ? ' class="' . $extra_css_classes . '"' : '') . '>' . $field['label'] . '</label>';
        }
        return $field['label'];
    }

    /**
     * Returns field's attribute
     *
     * @param string $field_name
     * @param string $attribute_name
     * @return mixed
     */
    public function getFieldAttribute($field_name, $attribute_name)
    {
        $field = $this->formbuilder->getField($field_name);
        if (!isset($field[$attribute_name])) {
            return FALSE;
        }

        return $field[$attribute_name];
    }

    /**
     * Return form builder
     *
     * @return FormBuilder
     */
    public function getFormBuilder()
    {
        return $this->formbuilder;
    }

    /**
     * Returns rendered input element associated with specified input
     *
     * @param $field_name
     * @param bool|callable $formatting_function_for_uneditable
     * @return bool|string
     */
    public function getFieldInput($field_name, $formatting_function_for_uneditable = FALSE)
    {
        $object = $this->formbuilder->getObject();
        $field = $this->formbuilder->getField($field_name);
        if (!$field) {
            return FALSE;
        }

        //TODO Check if this is not redundant, see the object population on database read
        $value = ($object && isset($object->$field_name) ? $object->$field_name : (isset($field['input_default_value']) && $field['input_default_value'] !== FALSE ? $field['input_default_value'] : ''));

        if ($field['input_type'] == FormBuilder::HIDDEN) {
            // Rendering hidden fields separately
            return $this->renderInput($field, $value, $object);
        }

        $output_element = '';
        if ($this->formbuilder->isReadOnly() || !$field['input_is_editable']) {
            switch ($field['input_type']) {
                case FormBuilder::IMAGE:
                    if ($value) {
                        $output_element .= '<a href="' . $field['upload_display_path'] . $value . '" class="image"><img src="admin/ajaxfilemanager/absolutethumb/100/' . $field['upload_display_path'] . $value . '" alt="" /></a>';
                    } else {
                        $output_element .= '';
                    }

                    break;
                case FormBuilder::SELECTBOX:
                    $output_element .= '<div class="input_hidden_description">';
                    if (isset($field['values'][$value])) {
                        $output_element .= $field['values'][$value];
                    } else {
                        $output_element .= '-';
                    }
                    $output_element .= '</div>';

                    break;

                default:
                    if (is_callable($formatting_function_for_uneditable)) {
                        $output_element .= call_user_func_array($formatting_function_for_uneditable, array($value));
                    } else {
                        if (is_array($value)) {
                            // Multiple select etc
                            foreach ($value as $v) {
                                if (isset($field['values'][$v])) {
                                    $output_element .= '<span class="multipleInput">' . htmlspecialchars($field['values'][$v]) . '</span>' . "\n";
                                }
                            }
                        } else {
                            if (isset($field['values'][$value])) {
                                $output_element = '<div class="input_hidden_description">' . htmlspecialchars($field['values'][$value]) . '</div>';
                            } else {
                                $value_html = $value;
                                if (!$value_html && $value_html !== 0) {
                                    $value_html = '-';
                                }
                                $output_element = '<div class="input_hidden_description">' . htmlspecialchars($value_html) . '</div>';
                            }
                        }
                    }

                    // Wrapping readonly value
                    $output_element = '<span id="' . $field['field'] . '_readonly">' . $output_element . '</span>';

                    break;
            }
            $hidden_field = $field;
            $hidden_field['input_type'] = FormBuilder::HIDDEN;
            $output_element .= $this->renderInput($hidden_field, $value, $object);
        } else {
            // Rendering input
            $output_element = $this->renderInput($field, $value, $object);
        }

        return $output_element;
    }

    /**
     * Returns field value by field name
     *
     * @param string $field_name
     * @return string
     */
    public function getFieldValue($field_name)
    {
        $object = $this->formbuilder->getObject();
        $field = $this->formbuilder->getField($field_name);
        if (!$field) {
            return FALSE;
        }

        $value = ($object && isset($object->$field_name) ? $object->$field_name : (isset($field['input_default_value']) && $field['input_default_value'] ? $field['input_default_value'] : ''));
        return $value;
    }

    /**
     * Returns rendered validation
     *
     * @return string
     */
    public function getValidation()
    {
        $output = '';
        if (isset($this->success) && !$this->success && !$this->formbuilder->getValidationErrorMessage()) {
            $error = CI_Controller::get_instance()->db->error();

            $message = CI_Controller::get_instance()->lang->line('formbuilder_label_unable_to_save') . ($error['message'] ? ' (SQL: ' . $error['code'] . ': ' . $error['message'] . ')' : ' (Method saveById returned FALSE)');

            if (count($this->formbuilder->getUploadWarnings())) {
                $message = $message . '<br><br><b>' . CI_Controller::get_instance()->lang->line('formbuilder_upload_warnings') . ':</b><br>' . implode('<br>', $this->formbuilder->getUploadWarnings());
            }

            $output .= $this->validation_message_prefix . $message . $this->validation_message_suffix;
        } elseif ($this->formbuilder->getValidationErrorMessage()) {
            $output .= $this->validation_message_prefix . $this->formbuilder->getValidationErrorMessage() . $this->validation_message_suffix;
        }

        $output .= validation_errors($this->validation_message_prefix, $this->validation_message_suffix);

        return $output;
    }

    /**
     * Returns field names of a given form
     *
     * @return array
     */
    public function getFieldNames()
    {
        return $this->formbuilder->getFieldNames();
    }

    /**
     * Sets the error delimiters used for formating validation message
     *
     * @param string $prefix
     * @param string $suffix
     */
    public function setErrorDelimiters($prefix, $suffix)
    {
        $this->validation_message_prefix = $prefix;
        $this->validation_message_suffix = $suffix;
    }

    /**
     * Renders the specified template
     *
     * @return string
     */
    protected function renderTemplate()
    {
        // Rendering
        ob_start();
        $form = &$this;
        /** @noinspection PhpIncludeInspection */
        include($this->template_absolute_path);
        $message = ob_get_contents();
        @ob_end_clean();
        return $message;
    }

    /**
     * Translates CI validation rules into JS library validation rules
     *
     * @param array $validation_rules
     * @return string
     */
    protected function translateCIValidationRulesToJSValidationEngineRules($validation_rules)
    {
        $validation_rules = trim($validation_rules);
        if (!$validation_rules) {
            return '';
        }

        $rules = array();

        $validation_rules = explode('|', $validation_rules);

        foreach ($validation_rules as $validation_rule) {
            if (strpos($validation_rule, '[')) {
                if (preg_match("/(.*)\[(.*)\]/", $validation_rule, $match)) {
                    $rules[$match[1]] = $match[2];
                }
            } else {
                $rules[$validation_rule] = $validation_rule;
            }
        }

        $extra_css_classes = array();

        foreach ($rules as $rule => $value) {
            switch ($rule) {
                case 'valid_email':
                    $extra_css_classes[] = 'custom[email]';
                    break;
                case 'numeric':
                    $extra_css_classes[] = 'number';
                    break;
                case 'required':
                    $extra_css_classes[] = 'required';
                    break;
                case 'min':
                    $extra_css_classes[] = 'min[' . $value . ']';
                    break;
                case 'max':
                    $extra_css_classes[] = 'max[' . $value . ']';
                    break;
                case 'min_length':
                    $extra_css_classes[] = 'minSize[' . $value . ']';
                    break;
                case 'max_length':
                    $extra_css_classes[] = 'maxSize[' . $value . ']';
                    break;
                case 'exact_length':
                    $extra_css_classes[] = 'minSize[' . $value . ']';
                    $extra_css_classes[] = 'maxSize[' . $value . ']';
                    break;
            }
        }

        return 'validate[' . implode(',', $extra_css_classes) . ']';
    }

    /**
     * Renders the input element
     *
     * @param array $field
     * @param mixed $value
     * @param object $object
     * @return string
     */
    protected function renderInput($field, $value, &$object)
    {
        $output_element = '';
        $extra_css_classes = $this->translateCIValidationRulesToJSValidationEngineRules($field['validation_rules']);

        if ($field['input_css_class']) {
            $extra_css_classes .= ' ' . $field['input_css_class'];
        }

        // Encoding if necessary
        if (!is_array($value)) {
            $value = htmlspecialchars($value);
        } else {
            foreach ($value as $key => $val) // Important do not use reference as the in_array function goes crazy!
            {
                $value[$key] = htmlspecialchars($val);
            }
        }

        //FIXME Check validation of multiselect/multicheckbox when not selecting any values

        switch ($field['input_type']) {
            case FormBuilder::CHECKBOX:
                // $value takes default value or object value but not POST
                // $this->object->$field['field'] takes object from DB or post
                if (!$value) {
                    $value = 1;
                }
                $is_checked = $value && isset($object->$field['field']) ? $value == $object->$field['field'] : $value == $field['input_default_value'];
                $output_element .= '<input type="checkbox" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $value . '"' . ($is_checked ? ' checked="checked"' : '') . ' class="' . $extra_css_classes . '"/>'; // $value='.$value.'; $default_value='.$field['input_default_value'].'; $object='.$object->$field['field'];
                break;


            case FormBuilder::SELECTBOX:
                if (is_array($field['values'])) {
                    //FIXME Set user defined default value in validation
                    $output_element .= '<select name="' . $field['field'] . '" id="' . $field['field'] . '" class="text' . $extra_css_classes . '" >' . "\n";
                    $was_selected = FALSE;
                    $selected_code = '';
                    foreach ($field['values'] as $key => $val) {
                        if (!$was_selected && $value == $key) // This is required to support null values, we can not use === operator neither to cast the values
                        {
                            $was_selected = TRUE;
                            $selected_code = ' selected="selected"';
                        }
                        $output_element .= '<option value="' . $key . '" ' . $selected_code . '>' . $val . '</option>' . "\n";
                        $selected_code = '';
                    }
                    $output_element .= '</select>' . "\n";
                }
                break;


            case FormBuilder::MULTIPLESELECT:
                if (is_array($field['values'])) {
                    $value = is_array($value) ? $value : array($value);

                    foreach ($field['values'] as $key => $val) {
                        $output_element .= '<span class="multipleInput select"><input type="checkbox" name="' . $field['field'] . '[' . $key . ']" id="' . $field['field'] . '[' . $key . ']" value="' . $key . '" ' . (in_array($key, $value) ? ' checked="checked"' : '') . ' /> <label for="' . $field['field'] . '[' . $key . ']">' . $val . '</label></span>' . "\n";
                    }
                }
                break;


            case FormBuilder::RADIO:
                if (is_array($field['values'])) {
                    // Setting default value
                    if ($field['input_default_value'] === FALSE) {
                        $field['input_default_value'] = NULL;
                    }
                    $value = $value ? $value : $field['input_default_value'];
                    $value = is_array($value) ? $value : array($value);

                    foreach ($field['values'] as $key => $val) {
                        $output_element .= '<span class="multipleInput radio"><input type="radio" name="' . $field['field'] . '" id="' . $field['field'] . '[' . $key . ']" value="' . $key . '" ' . (in_array($key, $value) ? ' checked="checked"' : '') . ' class="text' . $extra_css_classes . '" /> <label for="' . $field['field'] . '[' . $key . ']">' . $val . '</label></span>' . "\n";
                    }
                }
                break;


            case FormBuilder::MULTIPLECHECKBOX:
                if (is_array($field['values'])) {
                    // TODO Check validation - when field is required and no value is checked, the form builder selects all the fields (for example in system installer)
                    // Setting default value
                    if ($field['input_default_value'] === FALSE) {
                        $field['input_default_value'] = NULL;
                    }
                    $value = $value ? $value : $field['input_default_value'];
                    $value = is_array($value) ? array_merge(array(), $value) : array($value);

                    foreach ($field['values'] as $key => $val) {
                        $output_element .= '<span class="multipleInput checkbox"><input type="checkbox" name="' . $field['field'] . '[' . $key . ']" id="' . $field['field'] . '[' . $key . ']" value="' . $key . '" ' . (in_array($key, $value) ? ' checked="checked"' : '') . ' /> <label for="' . $field['field'] . '[' . $key . ']">' . $val . '</label></span>' . "\n";
                    }
                }
                break;


            case FormBuilder::TEXTAREA:
                $extra_attributes = '';
                if (preg_match("/(exact_length|max_length)\[(.*)\]/", $field['validation_rules'], $match)) {
                    $extra_attributes = 'maxlength="' . $match[2] . '"';
                }

                $output_element .= '<textarea name="' . $field['field'] . '" id="' . $field['field'] . '" class="text' . $extra_css_classes . '" rows="8" ' . $extra_attributes . '>' . $value . '</textarea>';
                break;


            case FormBuilder::RTF:
                CI_Controller::get_instance()->load->library('RTFEditor');
                CI_Controller::get_instance()->rtfeditor->setupDefaultConfig();

                if (isset($field['options']['rtf'])) {
                    foreach ($field['options']['rtf'] as $option_key => $option_value) {
                        CI_Controller::get_instance()->rtfeditor->setConfig($option_key, $option_value);
                    }
                }

                // FIXME Set user defined default value
                $output_element .= CI_Controller::get_instance()->rtfeditor->generate(htmlspecialchars_decode($value), 500, $field['field']);
                break;


            case FormBuilder::RTF_FULL:
                CI_Controller::get_instance()->load->library('RTFEditor');
                CI_Controller::get_instance()->rtfeditor->setupDefaultConfig();
                CI_Controller::get_instance()->rtfeditor->setFull();

                if (isset($field['options']['rtf'])) {
                    foreach ($field['options']['rtf'] as $option_key => $option_value) {
                        CI_Controller::get_instance()->rtfeditor->setConfig($option_key, $option_value);
                    }
                }

                // FIXME Set user defined default value
                $output_element .= CI_Controller::get_instance()->rtfeditor->generate(htmlspecialchars_decode($value), 500, $field['field']);
                break;


            case FormBuilder::IMAGE:

                CI_Controller::get_instance()->load->helper('number');

                $output_element .= '<input type="file" name="' . $field['field'] . '" id="' . $field['field'] . '" class="inputImage' . ($value ? ' hidden' : '') . '" />';
                if ($value) {
                    // Determining image extension
                    $image_extension = explode('.', $value);
                    if (count($image_extension) > 1) {
                        $image_extension = end($image_extension);
                        $image_extension = strtolower($image_extension);
                    } else {
                        $image_extension = FALSE;
                    }

                    $image_path = 'pepiscms/theme/img/ajaxfilemanager/broken_image_50.png';

                    $is_real_image = FALSE;
                    if (in_array($image_extension, array('jpg', 'jpeg', 'png', 'bmp', 'tiff'))) {
                        $is_real_image = TRUE;
                    }

                    if ($is_real_image) {
                        $image_path = 'admin/ajaxfilemanager/absolutethumb/100/' . $field['upload_display_path'] . $value;
                    } else if (file_exists(APPPATH . '/../theme/file_extensions/file_extension_' . $image_extension . '.png')) {
                        $image_path = 'pepiscms/theme/file_extensions/file_extension_' . $image_extension . '.png';
                    }

                    $output_element .= '<div class="form_image">' . "\n"; // Leave it as it is..
                    $output_element .= '    <div>' . "\n";


                    $output_element .= '        <a href="' . $field['upload_display_path'] . $value . '"' . ($is_real_image ? 'class=" image"' : 'class=" image_like" target="_blank"') . '><img src="' . $image_path . '" alt="" /></a>' . "\n";
                    $output_element .= '    </div>' . "\n";
                    $output_element .= '<div class="summary">';

                    $file_size = '';
                    $last_modified_at = CI_Controller::get_instance()->lang->line('formbuilder_file_not_found');
                    if (file_exists($field['upload_path'] . $value)) {
                        $file_size = byte_format(filesize($field['upload_path'] . $value));
                        $filemtime = filemtime($field['upload_path'] . $value);
                        $last_modified_at = date('Y-m-d', $filemtime) . '<br>' . date('H:i:s', $filemtime);
                    }

                    $output_element .= '<a href="#" class="remove_form_image" rel="' . $field['field'] . '" title="' . CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '">' . CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '</a><br>' . "\n";
                    $output_element .= strtoupper($image_extension) . ' ' . $file_size . '<br><br>' . $last_modified_at . '</div>';
                    $output_element .= '</div>';

                }

                $output_element .= '<input type="hidden" name="form_builder_files[' . $field['field'] . ']" value="' . $value . '" />' . "\n";
                $output_element .= '<input type="hidden" name="form_builder_files_remove[' . $field['field'] . ']" value="0" />' . "\n";
                break;


            case FormBuilder::MULTIPLEIMAGES:

                trigger_error('FormBuilder::MULTIPLEIMAGES is deprecated as PepisCMS 0.2.4.1', E_USER_DEPRECATED);

                $output_element .= '<input type="file" name="' . $field['field'] . '" id="' . $field['field'] . '" />';
                if ($value) {
                    $output_element .= '<div class="form_image">';
                    $output_element .= '<a href="' . $field['upload_display_path'] . $value . '" class="image"><img src="admin/ajaxfilemanager/absolutethumb/100/' . $field['upload_display_path'] . $value . '" alt="" /></a>';
                    $output_element .= '<a href="#" class="remove_form_image" rel="form_builder_files_remove[' . $field['field'] . ']" title="' . CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '">' . CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '</a>';
                    $output_element .= '</div>';
                }

                $output_element .= '<input type="hidden" name="form_builder_files[' . $field['field'] . ']" value="' . $value . '" />' . "\n";
                $output_element .= '<input type="hidden" name="form_builder_files_remove[' . $field['field'] . ']" value="0" />' . "\n";
                break;


            case FormBuilder::FILE:
                $output_element .= '<input type="file" name="' . $field['field'] . '" id="' . $field['field'] . '" class="inputFile' . ($value ? ' hidden' : '') . '" />';
                if ($value) {
                    $exploded_path = explode('/', $value);
                    $output_element .= '<div class="form_image"><a href="' . $field['upload_path'] . $value . '">' . end($exploded_path) . '</a>';
                    $output_element .= '<a href="#" class="remove_form_file" rel="' . $field['field'] . '" title="' . CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '">' . CI_Controller::get_instance()->lang->line('formbuilder_remove_file') . '</a></div>';
                }

                $output_element .= '<input type="hidden" name="form_builder_files[' . $field['field'] . ']" value="' . $value . '" />' . "\n";
                $output_element .= '<input type="hidden" name="form_builder_files_remove[' . $field['field'] . ']" value="0" />' . "\n";
                break;


            case FormBuilder::HIDDEN:
                if (is_array($value)) {
                    // This only happens for multiple checkbox when the field is not editable
                    foreach ($value as $a_value) {
                        $output_element .= '<input type="hidden" name="' . $field['field'] . '[]" id="' . $field['field'] . '_' . str_replace('"', '', $a_value) . '" value="' . $a_value . '" />';
                    }
                } else {
                    $output_element .= '<input type="hidden" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $value . '" />';
                }

                break;


            case FormBuilder::DATE:
                $output_element .= $this->includeJavaScript();

                $output_element .= '<input type="text" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $value . '" class="text date' . $extra_css_classes . '" />';
                $output_element .= '<script type="text/javascript">$("#' . $field['field'] . '").datepicker({dateFormat: "yy-mm-dd", changeYear: true, changeMonth: true, yearRange: \'' . (date('Y') - 100) . ':c+10\' });</script>';
                break;


            case FormBuilder::TIMESTAMP:
                $output_element .= $this->includeJavaScript();

                $output_element .= '<input type="text" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $value . '" class="text date' . $extra_css_classes . '" />';
                $output_element .= '<script type="text/javascript">$("#' . $field['field'] . '").datetimepicker({dateFormat: "yy-mm-dd", timeFormat: "HH:mm:ss", changeYear: true, changeMonth: true, yearRange: \'' . (date('Y') - 100) . ':c+10\' });</script>';
                break;


            case FormBuilder::PASSWORD:
                $output_element .= '<input type="password" name="' . $field['field'] . '" id="' . $field['field'] . '" value="" autocomplete="off" class="text' . $extra_css_classes . '" />';
                break;


            case FormBuilder::TEXTFIELD_AUTOCOMPLETE:
                $output_element .= $this->includeJavaScript();
                $output_element .= '<input type="text" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $value . '" class="text' . $extra_css_classes . '" />';
                $output_element .= '<script type="text/javascript">$("#' . $field['field'] . '").autocomplete({source: "' . $field['autocomplete_source'] . '", minLength: 1});</script>';
                break;


            case FormBuilder::SELECTBOX_AUTOCOMPLETE:
                $output_element .= $this->includeJavaScript();
                $output_element .= '<input type="hidden" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $value . '" />';

                // FIXME Remove POST workaround
                $field_ac_label = $field['field'] . '_ac_label';
                $output_element .= '<input type="text" name="' . $field_ac_label . '" id="' . $field_ac_label . '" value="' . (isset($_POST[$field_ac_label]) ? $_POST[$field_ac_label] : '') . '" class="text' . $extra_css_classes . '" />';
                $output_element .= '<script type="text/javascript">$("#' . $field['field'] . '_ac_label").autocomplete({source: "' . $field['autocomplete_source'] . '", minLength: 1, select: function(event, ui) {
                if( !ui.item || ui.item == undefined ) return;
                $("#' . $field['field'] . '").val(""+ui.item.id);
            }});</script>';
                break;


            case FormBuilder::COLORPICKER:
                $output_element .= $this->includeJavaScript();

                $output_element .= '<input type="text" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $value . '" class="text corolpicker ' . $extra_css_classes . '" maxlength="6" size="6" />';
                $output_element .= '<script type="text/javascript">$("#' . $field['field'] . '").colpick({layout:"hex", onChange:function(hsb,hex,rgb,el,bySetColor){$(el).css("border-right", "solid 20px #"+hex);if(!bySetColor) $(el).val(hex);},onSubmit:function(hsb,hex,rgb,el) {$(el).css("border-right", "solid 20px #"+hex);$(el).colpickHide();}}).keyup(function(){$(this).colpickSetColor(this.value);}).css("border-right", "solid 20px #"+$("#' . $field['field'] . '").val());</script>';
                break;

            default:

                $extra_attributes = '';
                if (preg_match("/(exact_length|max_length)\[(.*)\]/", $field['validation_rules'], $match)) {
                    $extra_attributes = 'maxlength="' . $match[2] . '"';
                }

                $output_element .= '<input type="text" name="' . $field['field'] . '" id="' . $field['field'] . '" value="' . $value . '" class="text' . $extra_css_classes . '" ' . $extra_attributes . ' />';
                break;
        }
        return $output_element;
    }

    /**
     * Returns HTML required by UI controls
     *
     * @return string
     */
    private function includeJavaScript()
    {
        $output_element = '';
        if (!$this->is_js_included) {
            $this->is_js_included = TRUE;
            $output_element .= '<link href="pepiscms/3rdparty/jquery-ui/theme/smoothness/jquery-ui.custom.css" rel="stylesheet" type="text/css"/>' . "\n";
            $output_element .= '<link href="pepiscms/3rdparty/jquery-ui/jquery-ui.timepicker.css" rel="stylesheet" type="text/css"/>' . "\n";
            $output_element .= '<script type="text/javascript" src="pepiscms/3rdparty/jquery-ui/jquery-ui.custom.min.js?v=' . PEPISCMS_VERSION . '"></script>' . "\n";
            $output_element .= '<script type="text/javascript" src="pepiscms/3rdparty/jquery-ui/jquery-ui.timepicker.js?v=' . PEPISCMS_VERSION . '"></script>' . "\n";
            // Order of elements matters
            $output_element .= '<script type="text/javascript" src="pepiscms/3rdparty/jquery-ui/language/jquery.ui.datepicker-pl.js"></script>' . "\n";
            $output_element .= '<script type="text/javascript" src="pepiscms/3rdparty/jquery-ui/language/jquery.ui.datepicker-en.js"></script>' . "\n";

            $output_element .= '<link href="pepiscms/3rdparty/colpick/css/colpick.css" rel="stylesheet" type="text/css"/>' . "\n";
            $output_element .= '<script type="text/javascript" src="pepiscms/3rdparty/colpick/js/colpick.js"></script>' . "\n";
        }

        return $output_element;
    }

}

/**
 * Floating renderer for FormBuilder
 *
 * Floating form renders inputs in the same row.
 * @since 0.1.5
 */
class FloatingFormRenderer extends DefaultFormRenderer
{

    public function __construct($template_absolute_path = FALSE)
    {
        parent::__construct(APPPATH . 'views/templates/formbuilder_floating.php');
    }

}