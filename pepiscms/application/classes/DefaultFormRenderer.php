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
class DefaultFormRenderer implements FormRenderableInterface
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
     * @inheritdoc
     */
    public function render($formbuilder, $success)
    {
        $this->formbuilder = $formbuilder; // Required!
        $this->success = $success;

        if (!$this->template_absolute_path || !file_exists($this->template_absolute_path)) {
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
        $value = $this->computeValue($field_name, $object, $field);

        $component = $this->resolveComponent($field['input_type']);

        if (($this->formbuilder->isReadOnly() || !$field['input_is_editable']) && $field['input_type'] != FormBuilder::HIDDEN) {
            $component_html = $this->computeReadOnlyComponentHtml($field, $value, $object, $component, $formatting_function_for_uneditable);
            return $this->wrapReadOnlyComponent($field, $component_html);
        } else {
            return $this->renderInput($field, $value, $object, $component, FALSE);
        }
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
        if (($object && isset($object->$field_name))) {
            return $object->$field_name;
        } else {
            return (isset($field['input_default_value']) && $field['input_default_value'] ? $field['input_default_value'] : '');
        }
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

            $message = CI_Controller::get_instance()->lang->line('formbuilder_label_unable_to_save') .
                ($error['message'] ? ' (SQL: ' . $error['code'] . ': ' . $error['message'] . ')' : ' (Method saveById returned FALSE)');

            if (count($this->formbuilder->getUploadWarnings())) {
                $message = $message . '<br><br><b>' .
                    CI_Controller::get_instance()->lang->line('formbuilder_upload_warnings') . ':</b><br>' .
                    implode('<br>', $this->formbuilder->getUploadWarnings());
            }

            $output .= $this->validation_message_prefix . $message . $this->validation_message_suffix;
        } elseif ($this->formbuilder->getValidationErrorMessage()) {
            $output .= $this->validation_message_prefix . $this->formbuilder->getValidationErrorMessage() .
                $this->validation_message_suffix;
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
        $form = &$this; // $form variable can be used within included file
        /** @noinspection PhpIncludeInspection */
        include($this->template_absolute_path);
        $message = ob_get_contents();
        @ob_end_clean();
        return $message;
    }

    /**
     * Renders the input element
     *
     * @param array $field
     * @param mixed $value
     * @param object $object
     * @param $component
     * @return string
     */
    private function renderInput($field, $value, &$object, $component, $readOnly)
    {
        $extra_css_classes = $this->computeExtraCssClasses($field);

        // Encoding if necessary
        if (!is_array($value)) {
            $valueEscaped = htmlspecialchars($value);
        } else {
            $valueEscaped = array();
            foreach ($value as $key => $val) // Important do not use reference as the in_array function goes crazy!
            {
                $valueEscaped[$key] = htmlspecialchars($val);
            }
        }

        $output = '';

        if ($readOnly) {
            $output .= $component->renderReadOnlyComponent($field, $value, $valueEscaped, $object);
        } else {
            if ($component->shouldAttachAdditionalJavaScript()) {
                $output .= $this->includeJavaScript();
            }

            $output .= $component->renderComponent($field, $value, $valueEscaped, $object, $extra_css_classes);
        }

        return $output;
    }

    /**
     * @param $componentId
     * @return null|\Piotrpolak\Pepiscms\Formbuilder\Component\ComponentInterface
     */
    private function resolveComponent($componentId)
    {
        /**
         * @var $registeredComponents \Piotrpolak\Pepiscms\Formbuilder\Component\ComponentInterface[]
         */
        $registeredComponents = array(
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Textfield(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Password(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Checkbox(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Selectbox(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\MultipleSelect(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Radio(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Textarea(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Image(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\File(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Hidden(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Rtf(FALSE),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Rtf(TRUE),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\MultipleCheckbox(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Date(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Timestamp(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\Colorpicker(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\TextfieldAutocomplete(),
            new \Piotrpolak\Pepiscms\Formbuilder\Component\SelectboxAutocomplete(),
        );

        foreach ($registeredComponents as $registeredComponent) {
            if ($registeredComponent->getComponentId() == $componentId) {
                return $registeredComponent;
            }
        }
        return $registeredComponents[0];
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

    /**
     * @param $field
     * @param $output_element
     * @return string
     */
    private function wrapReadOnlyComponent($field, $output_element)
    {
        return '<span id="' . $field['field'] . '_readonly">' . $output_element . '</span>';
    }

    /**
     * @param $field
     * @param $valueEscaped
     * @param $object
     * @param $component
     * @param $formatting_function_for_uneditable
     * @return array
     */
    private function computeReadOnlyComponentHtml($field, $valueEscaped, $object, \Piotrpolak\Pepiscms\Formbuilder\Component\ComponentInterface $component, $formatting_function_for_uneditable)
    {
        if (is_callable($formatting_function_for_uneditable)) {
            return call_user_func_array($formatting_function_for_uneditable, array($valueEscaped));
        } else {
            $output = $this->renderInput($field, $valueEscaped, $object, $component, TRUE);

            if ($component->shouldRenderHiddenForReadOnly()) {
                $output .= $this->renderInput($field, $valueEscaped, $object, new \Piotrpolak\Pepiscms\Formbuilder\Component\Hidden(), FALSE);
            }

            return $output;
        }
    }

    /**
     * @param $field_name
     * @param $object
     * @param $field
     * @return string
     */
    private function computeValue($field_name, $object, $field)
    {
        if ($object && isset($object->$field_name)) {
            $value = $object->$field_name;
        } else {
            if (isset($field['input_default_value']) && $field['input_default_value']) {
                $value = $field['input_default_value'];
            } else {
                $value = '';
            }
        }
        return $value;
    }

    /**
     * @param $field
     * @return string
     */
    private function computeExtraCssClasses($field)
    {
        $validationRulesTranslator = new Piotrpolak\Pepiscms\Formbuilder\Component\ValidationRulesTranslator();
        $extra_css_classes = $validationRulesTranslator->translateCIValidationRulesToJSValidationEngineRules($field['validation_rules']);

        if ($field['input_css_class']) {
            $extra_css_classes .= ' ' . $field['input_css_class'];
        }
        return $extra_css_classes;
    }
}
