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
 * Default renderer for FormBuilder
 *
 * Default renderer is often extended by custom renderers
 *
 * @since 0.1.5
 */
class DefaultFormRendererInterface implements FormRenderableInterface
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
