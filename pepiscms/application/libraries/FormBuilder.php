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
            $this->input_groups[$defaults['input_group']] = array(
                'label' => ucfirst(str_replace('_', ' ', $defaults['input_group'])),
                'description' => FALSE,
                'fields' => array()
            );
        }
        $this->input_groups[$defaults['input_group']]['fields'][] = $defaults['field'];


        if ($defaults['input_type'] == FormBuilder::IMAGE || $defaults['input_type'] == FormBuilder::FILE) {
            $this->file_upload_fields[$defaults['field']] = $defaults['field'];
            $defaults['validation_rules'] = '';
            if ($defaults['upload_allowed_types'] === FALSE && $defaults['input_type'] == FormBuilder::IMAGE) {
                $defaults['upload_allowed_types'] = 'jpg|jpeg|gif|png|bmp';
            }
        }

        $defaults['label'] = $defaults['label'] !== FALSE ?
            $defaults['label'] : ucfirst(str_replace('_', ' ', $defaults['field']));

        $this->fields[$defaults['field']] = $defaults;

        return true;
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
            $this->instance_code = isset($_POST['form_builder_instance_code']) ?
                $_POST['form_builder_instance_code'] : time() . '' . rand(10000, 99999);
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
     * @param FormRenderableInterface $renderer
     * @return bool
     */
    public function setRenderer(FormRenderableInterface $renderer)
    {
        $this->renderer = $renderer;
        return TRUE;
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

        $this->generateSetNoCacheHeaders();

        // Default value
        $save_success = TRUE;


        // Checking if the request was sent by a form builder generated form
        // Saving if POST form_builder_id is present
        if (isset($_POST['form_builder_id'])) {
            $this->setId($_POST['form_builder_id']);
            unset($_POST['form_builder_id']);

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

                // CALLBACK before save
                if (isset($this->callbacks[self::CALLBACK_BEFORE_SAVE])) {
                    call_user_func_array($this->callbacks[self::CALLBACK_BEFORE_SAVE], array(&$save_array));
                }

                try {
                    $save_success = $this->generateDoSave($save_array);
                } catch (Exception $e) {
                    $save_success = FALSE;
                    $this->setValidationErrorMessage($e->getMessage());
                    Logger::error('Unable to save. Exception ' . get_class($e) . ' ' . $e->getTraceAsString(),
                        'FORMBUILDER');
                }

                if ($save_success) {
                    $is_apply = CI_Controller::get_instance()->input->post('apply') !== NULL;

                    if (!$this->getId()) { // There were no ID, try to determine it after the form is saved
                        $this->generateRefreshId($save_success);
                    }

                    if ($this->getId()) {
                        $this->generateHandleForeignKeyManyToManyUpdate($save_array);
                    }

                    if ($this->use_simple_session_message_on_save_success) {
                        $this->generateSetSuccessMessage();
                    }

                    if (isset($this->callbacks[self::CALLBACK_AFTER_SAVE])) {
                        call_user_func_array($this->callbacks[self::CALLBACK_AFTER_SAVE], array(&$save_array));
                    }

                    $this->generateHandleRedirectOnSuccess($is_apply);

                } else {
                    if (isset($this->callbacks[self::CALLBACK_ON_SAVE_FAILURE])) {
                        call_user_func_array($this->callbacks[self::CALLBACK_ON_SAVE_FAILURE], array(&$save_array));
                    }
                }
            }

            // !
            // Regenerating the object from POST
            $this->generateRecreateObjectFromSaveArray($save_array);
        } // END POST, END OF FORM SAVE


        else {
            // Executed for situation where not a POST save and no object found
            // This is called if there is no POST - reading object from the database
            if (!$this->object) {
                $this->generateDoReadObject();
                $this->generateDoAssignDefaultValuesForEmptyReadFields();
            }
        }

        foreach ($this->fields as &$field) {
            if ($field['foreign_key_table']) {
                $this->generateForeignKeyFetchObjectValues($field);
                $this->generateForeignKeyFillFieldPossibleValues($field);
            }
        }


        // CALLBACK
        if (isset($this->callbacks[self::CALLBACK_BEFORE_RENDER])) {
            call_user_func_array($this->callbacks[self::CALLBACK_BEFORE_RENDER], array(&$this->object));
        }

        // Returns rendered HTML
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
            $save_array[$field['field']] = CI_Controller::get_instance()->input->post($field['field']) !== NULL ?
                CI_Controller::get_instance()->input->post($field['field']) : '';

            if (!$save_array[$field['field']] && $field['foreign_key_accept_null']) {
                $save_array[$field['field']] = NULL;
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
        CI_Controller::get_instance()->load->library('SimpleSessionMessage');

        if (count($this->getUploadWarnings())) {
            CI_Controller::get_instance()->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_NOTIFICATION);
            CI_Controller::get_instance()->simplesessionmessage->setRawMessage(CI_Controller::get_instance()->lang->line('formbuilder_form_successfully_saved') . '<br><br><b>' . CI_Controller::get_instance()->lang->line('formbuilder_upload_warnings') . ':</b><br>' . implode('<br>', $this->getUploadWarnings()));
        } else {
            CI_Controller::get_instance()->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
            CI_Controller::get_instance()->simplesessionmessage->setMessage('formbuilder_form_successfully_saved');
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
    }

    /**
     * @param $field
     * @return bool
     */
    private function generateEnsureUploadDirectoryExits($field)
    {
        // If file exist, then checking if not a directory
        if (file_exists($field['upload_path'])) {
            // Checking if something else than a directory
            if (!is_dir($field['upload_path'])) {
                Logger::error('Upload path is a regular file and not a directory ' . $field['upload_path'], 'FORMBUILDER');
                return FALSE;
            }
        } else {
            // If the file does not exist, attempt to create the path
            if (!@mkdir($field['upload_path'])) {
                Logger::error('Unable to create directory ' . $field['upload_path'], 'FORMBUILDER');
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * @param $field_name
     * @return array
     */
    private function generateUploadComputeIndexesToRemove($field_name)
    {
        // Messy code, do not touch
        // $fileIndexesToRemove start with 1! not with 0
        $file_indexes_to_remove = array();

        // Checking if a given field was marked as to be deleted
        // $_POST['form_builder_files_remove'] field is a hidden field generated by the JavaScript
        if (isset($_POST['form_builder_files_remove'][$field_name])) {
            if (!is_array($_POST['form_builder_files_remove'][$field_name])) {
                $_POST['form_builder_files_remove'][$field_name] = array($_POST['form_builder_files_remove'][$field_name]);
            }

            foreach ($_POST['form_builder_files_remove'][$field_name] as $i) {
                if ($i > 0) {
                    $file_indexes_to_remove[] = $i;
                }
            }
        }
        return $file_indexes_to_remove;
    }

    /**
     * @param $field_name
     * @return array
     */
    private function generateComputeFileFields($field_name)
    {
        // $form_builder_files must be an array
        // TODO Remove multiple file
        $form_builder_field_files = array();
        if (isset($_POST['form_builder_files'][$field_name])) {
            // TODO Standardize array file fields
            if (!is_array($_POST['form_builder_files'][$field_name]) && strlen($_POST['form_builder_files'][$field_name]) > 0) {
                // TODO Standardize array file fields
                $form_builder_field_files = array($_POST['form_builder_files'][$field_name]);
            }
        }
        return $form_builder_field_files;
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
            CI_Controller::get_instance()->db->set($field['foreign_key_junction_id_field_left'], $this->getId());
            // The following if allows to have multiple form fields having relations with the same foreign_key_junction_table
            if (count($where_conditions) > 0) {
                CI_Controller::get_instance()->db->set($where_conditions);
            }
            CI_Controller::get_instance()->db->set($field['foreign_key_junction_id_field_right'], $right_id)
                ->insert($field['foreign_key_junction_table']);
        }
    }

    /**
     * @param $field
     * @param $where_conditions
     */
    private function generateForeignKeyManyToManyDoDelete($field, $where_conditions)
    {
        CI_Controller::get_instance()->db->where($field['foreign_key_junction_id_field_left'], $this->getId());
        // The following if allows to have multiple form fields having relations with the same foreign_key_junction_table
        if (count($where_conditions) > 0) {
            CI_Controller::get_instance()->db->where($where_conditions);
        }
        CI_Controller::get_instance()->db->delete($field['foreign_key_junction_table']);
    }

    /**
     * @param $save_array
     * @return void
     */
    private function generateHandleForeignKeyManyToManyUpdate($save_array)
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
        // CALLBACK
        if (isset($this->callbacks[self::CALLBACK_ON_READ])) {
            // There is on read callback, the object is retrieved using the callback function
            // The callback function must take the object (empty) by reference and must fill it
            call_user_func_array($this->callbacks[self::CALLBACK_ON_READ], array(&$this->object));
        } elseif ($this->feed_object && $this->id) {
            // No callback, read the object from the feed
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
                if (!isset($this->object->$field['field'])) {
                    $this->object->$field['field'] = (isset($field['input_default_value']) && $field['input_default_value'] !== FALSE ? $field['input_default_value'] : '');
                }
            }
        }
    }

    private function generateSetNoCacheHeaders()
    {
        CI_Controller::get_instance()->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        CI_Controller::get_instance()->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        CI_Controller::get_instance()->output->set_header('Cache-Control: post-check=0, pre-check=0');
        CI_Controller::get_instance()->output->set_header('Pragma: no-cache');
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

        CI_Controller::get_instance()->load->library('upload');
        foreach ($this->file_upload_fields as $upload_field_name) {
            if (!$this->generateEnsureUploadDirectoryExits($this->fields[$upload_field_name])) {
                continue;
            }

            $file_indexes_to_remove = $this->generateUploadComputeIndexesToRemove($upload_field_name);

            // TODO $this->fields[$field] might be dangerous when no keys are specified (field name specified as an element of the array)
            // Reinitializing upload - necessary for consequent file uploads
            CI_Controller::get_instance()->upload->initialize($this->generateGetUploadConfig($upload_field_name));

            // Needed for resetting field value, especially for the case when we do not overwrite the previous files
            $is_any_file_removed = false;

            $path_to_remove = false;
            $form_builder_field_files = $this->generateComputeFileFields($upload_field_name);
            // Removing user marked files
            // TODO Check if array is needed
            foreach ($file_indexes_to_remove as $file_index_to_remove) {
                $i = $file_index_to_remove - 1;
                $path_to_remove = $this->fields[$upload_field_name]['upload_path'] . $form_builder_field_files[$i];
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


            $upload = CI_Controller::get_instance()->upload->do_upload($upload_field_name);

            if (!$upload) {
                // Logging all errors except upload_no_file_selected
                if ($this->hasSignificantUploadError()) {
                    $this->upload_warnings += CI_Controller::get_instance()->upload->error_msg;
                }

                // On failure removing field, so that the original image is kept
                if (!$is_any_file_removed) {
                    unset($save_array[$upload_field_name]);
                }
            } else {
                $data = CI_Controller::get_instance()->upload->data();
                $filename = $data['file_name'];

                // Removing the previous image/file - overwriting
                if (($this->fields[$upload_field_name]['input_type'] == FormBuilder::IMAGE
                        || $this->fields[$upload_field_name]['input_type'] == FormBuilder::FILE)
                    && isset($form_builder_field_files[0])) {

                    $file_to_remove = $this->fields[$upload_field_name]['upload_path'] . $form_builder_field_files[0];
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
                if ($this->fields[$upload_field_name]['upload_complete_callback']) {
                    call_user_func_array($this->fields[$upload_field_name]['upload_complete_callback'],
                        array(&$filename,
                            &$this->fields[$upload_field_name]['upload_path'],
                            &$save_array,
                            $upload_field_name));
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
                    $save_array[$upload_field_name] = '';
                }
            } // If there is a single file, add only the given file
            elseif ($count_form_builder_field_files == 1 && isset($form_builder_field_files[0])) {
                $save_array[$upload_field_name] = $form_builder_field_files[0];
            } // Otherwise glue the files
            else {
                // TODO Remove multiple files
                $save_array[$upload_field_name] = implode(';', $form_builder_field_files);
            }
        }
    }

    /**
     * @param $upload_field_name
     * @return array
     */
    private function generateGetUploadConfig($upload_field_name)
    {
        $config = array();
        $config['upload_path'] = $this->fields[$upload_field_name]['upload_path'];
        $config['allowed_types'] = $this->fields[$upload_field_name]['upload_allowed_types'];
        $config['encrypt_name'] = $this->fields[$upload_field_name]['upload_encrypt_name'];
        return $config;
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
                $save_array[$field['field']] = (isset($save_array[$field['field']]) && $save_array[$field['field']] ? TRUE : FALSE);
            }
        }
    }

    /**
     * @param $field
     */
    private function generateConvertComasIntoDotsForNumericTypes($field)
    {
        if (strpos($field['validation_rules'], 'numeric') !== FALSE
            && CI_Controller::get_instance()->input->post($field['field']) !== NULL) {
            $replaced = str_replace(',', '.', CI_Controller::get_instance()->input->post($field['field']));
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
            $field_validation_rules = $field['validation_rules'] ? $field['validation_rules'] : FALSE;

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
        $save_success = FALSE;
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
            }

        } else {
            // END Prevent from redirecting on apply
            CI_Controller::get_instance()->output->set_header('X-XSS-Protection: 0');
        }
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

                $this->object->$field['field'] = CI_Controller::get_instance()
                    ->Generic_model->getAssocPairs($field['foreign_key_junction_id_field_right'],
                        $field['foreign_key_junction_id_field_right'], $field['foreign_key_junction_table'],
                        FALSE,
                        FALSE,
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
        $isValid = TRUE;
        // Setting validation rules if any of them exist
        if (count($validation_rules) > 0) {
            CI_Controller::get_instance()->form_validation->set_rules($validation_rules);
            $isValid = CI_Controller::get_instance()->form_validation->run() === TRUE;
        }
        return $isValid;
    }

    /**
     * @param $field
     * @return mixed
     */
    private function generateForeignKeyFillFieldPossibleValues(&$field)
    {
        $should_fetch = FALSE;
        if (!$field['input_is_editable']) {
            // is_array is required for multiple checkbox fields, etc - avoiding errors
            if (isset($this->object->$field['field'])) {
                $possible_values = (is_array($this->object->$field['field'])
                    ? $this->object->$field['field'] : array($this->object->$field['field']));
            } else {
                $possible_values = array($field['input_default_value']);
            }
            $should_fetch = true;

        } elseif (!is_array($field['values'])) {
            $should_fetch = true;
            $possible_values = FALSE;
        }


        if ($should_fetch) {
            $field['values'] = CI_Controller::get_instance()->Generic_model->getAssocPairs($field['foreign_key_field'],
                $field['foreign_key_label_field'],
                $field['foreign_key_table'],
                FALSE,
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
        $this->id = CI_Controller::get_instance()->db->insert_id();

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
        $CI = CI_Controller::get_instance();
        return isset($CI->upload->error_msg[0]) && $CI->upload->error_msg[0] != $CI->lang->line('upload_no_file_selected');
    }
}