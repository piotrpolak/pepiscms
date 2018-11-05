<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * {module_name} admin controller
 *
 * @author {author}
 * @date {date}
 * @see AdminCRUDController for the list of the methods you can use in your constructor
 * @classTemplateVersion 20181105
 *
 * @property {model_class_name} ${model_class_name}
 */
class {module_class_name}Admin extends AdminCRUDController
{
    /**
     * Base path for file uploads
     *
     * @var String
     */
    private $uploads_base_path = './application/cache/tmp/'; // Overwritten by constructor

    /**
     * Default constructor containing all necessary definitions
     */
    public function __construct()
    {
        parent::__construct();

        // Overwriting uploads base path
        $this->uploads_base_path = $this->config->item('uploads_path') . '{module_name}/';

        // Getting module and model name from class name
        $module_name = $this->getModuleName();

        $this->load->moduleLanguage($module_name, $module_name);
        $this->load->moduleModel($module_name, '{model_class_name}');

        $this->setFeedObject($this->{model_class_name})
            ->setPageTitle($this->lang->line($module_name . '_module_name'))
            ->setAddNewItemLabel($this->lang->line($module_name . '_add'));
//        $this->addActionForIndex(array('link' => module_url() . 'export', 'name' => $this->lang->line($module_name . '_export'), 'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png'));
//        if ($this->input->getParam('id')) {
//            $this->addActionForEdit(array('link' => module_url('attachments') . 'index/layout-popup/forced_filters-' . DataGrid::encodeFiltersString(array('entry_id' => $this->input->getParam('id'))), 'name' => $this->lang->line($module_name . '_manage_attachments'), 'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png', 'class' => 'popup'));
//        }
//        $this->setBackToItemsLabel($this->lang->line($module_name . '_back'));
//        $this->setTooltipTextForIndex($this->lang->line($module_name . '_index_tip'));
//        $this->setTooltipTextForEdit($this->lang->line($module_name.'_edit_tip'));

        // Setting crud properties, these are optional. Default true all
        $this->setDeletable(true)
            ->setAddable(true)
            ->setEditable(true)
            ->setPreviewable(false)
            ->setPopupEnabled(false)
            ->setOrderable(false);

//        $this->setExportable(true, function ($resulting_line) {
//            // The callback is optional, it is used to filter individual rows before inserting them
//            unset($resulting_line['afield_you_want_to_ignore']);
//            return $resulting_line;
//        });

//        $this->setImportable(true, array('name', 'street'), function ($resulting_line, $original_line) {
//            // The callback is optional, it is used to filter individual rows before inserting them
//            // You can change existing values, add or unset fields
//            $resulting_line['name'] = ucfirst($resulting_line['name']);
//            $resulting_line['is_displayed_globally'] = 1;
//            $resulting_line['is_published'] = 1;
//            return $resulting_line;
//        });


        $this->setMetaOrderField('{label_field_name}', $this->lang->line($module_name . '_{label_field_name}'));
        $this->setMetaTitlePattern('{{label_field_name}}'); // Use field names as {field_name}
        {image_meta_code_element}
//        $this->setMetaImageField('image_path', $this->uploads_base_path); // Using field name and basepath
//        $this->setMetaImageField(array($this, '_datagrid_format_image_field')); // Using callback
        {description_meta_code_element}
//        $this->setMetaDescriptionPattern('{description}'); // Use field names as {field_name}
//        $this->setMetaDescriptionPattern(array($this, '_fb_format_meta_description')); // Can use a callback as well
//        $this->setMetaDescriptionPattern('{description}', array($this, '_fb_format_meta_description')); // Can use a pattern + callback as well
        {order_meta_code_element}
//        $this->setOrderable(true, 'item_order');
//        $this->setOrderable(true, 'item_order', 'optional_constraint_field_name'); // optional_constraint_field_name is usually the foreign key field
//        $this->setStarable(true, 'is_starable', $this->lang->line($module_name . '_is_starable')); // Basic toggle button accessible from grid
//        $this->addMetaAction(array($this, '_crud_action_images'), $this->lang->line($module_name . '_images')); // Action link displayed in grid cell
//        $this->addMetaAction(array($this, '_crud_action_images'), $this->lang->line($module_name . '_images'), 'heavy_operation'); // Action link displayed in grid cell that triggers heavy operation overlayer
//        $this->addActionForIndex(array('title' => $this->lang->line($module_name.'_manage_banners'), 'name' => $this->lang->line($module_name.'_manage_banners'), 'icon' => 'pepiscms/theme/img/dialog/actions/action_16.png', 'link' => module_url().'manage_banners'));


//        // Manually restrict access to selected items
//        if (!$this->auth->isRoot()) {
//            // Display rows that match the user_id parameter
//            $this->manuallySetForcedFilters(array('user_id' => $this->auth->getUserId()));
//        }


//        // Datagrid row format callback
//        $this->datagrid->setRowCssClassFormattingFunction(function ($line) {
//            if ($line->is_active == 1) {
//                return DataGrid::ROW_COLOR_GREEN;
//            } else {
//                return DataGrid::ROW_COLOR_RED;
//            }
//        });


//        $this->datagrid->setItemsPerPage(300)
//            ->datagrid->setDefaultOrder('id', 'DESC')
//            ->datagrid->addFilter($this->lang->line($module_name.'_date_from'), 'date', DataGrid::FILTER_DATE, false,
//                DataGrid::FILTER_CONDITION_GREATER_OR_EQUAL); // Can also be an associative array
        {filters_element}



        // If not set, then DefaultFormRenderer is used
        // You can even use your own form templates, see views/templates
        $this->formbuilder->setRenderer(new FloatingFormRenderer())
            ->setApplyButtonEnabled(true);


        // Formbuilder callbacks
        $callbacks = array(
            '_fb_callback_after_save' => FormBuilder::CALLBACK_AFTER_SAVE,
            '_fb_callback_before_render' => FormBuilder::CALLBACK_BEFORE_RENDER,
            '_fb_callback_before_save' => FormBuilder::CALLBACK_BEFORE_SAVE,
            '_fb_callback_on_save' => FormBuilder::CALLBACK_ON_SAVE,
            '_fb_callback_on_save_failure' => FormBuilder::CALLBACK_ON_SAVE_FAILURE,
            '_fb_callback_on_read' => FormBuilder::CALLBACK_ON_READ,
        );
        // Assigning every single callback
        foreach ($callbacks as $callback_method_name => $callback_type) {
            // Attaching only when are callable
            if (is_callable(array($this, $callback_method_name))) {
                $this->formbuilder->setCallback(array($this, $callback_method_name), $callback_type);
            }
        }


        $definition = {definition_output}

        // This method is only required when using $this->setTable()
        // or the model has not accepted post fields defined
        //
        // Feel free to comment it out if post accepted fields are defined in model
//        $this->getFeedObject()->setAcceptedPostFields(array_keys($definition));

//        // Generating form fields for translations
//        // Only if the feed object is translateable and the getLocales function exists
//        if (($this->getFeedObject() instanceof TranslateableInterface) && is_callable(array($this, 'getLocales'))) {
//            // Overwriting the default group
//            foreach ($definition as &$field) {
//                // Only when there is no group specified
//                if (!isset($field['input_group']) || $field['input_group'] == 'default') {
//                    // Take the default action name
//                    $field['input_group'] = $module_name . '_add';
//                }
//            }
//
//            // Getting language list
//            $locales = $this->getLocales();
//            // First language is the default one and is not taken into consideration
//            unset($locales[0]);
//
//            // Copying fields for each language
//            foreach ($locales as $locale) {
//                foreach ($this->getFeedObject()->getTranslateableFieldNames() as $translateable_field) {
//                    $definition[$translateable_field . '_' . $locale] = $definition[$translateable_field];
//                    $definition[$translateable_field . '_' . $locale]['input_group'] = $locale;
//                }
//            }
//        }

        // Here we go!
        $this->setDefinition($definition);
    }

    /**
     * Description format callback
     *
     * @param mixed $content Value of the element
     * @param object $line Object representing database row
     * @return string resulting text/html
     */
    public function _fb_format_meta_description($content, $line)
    {
        $this->load->helper('text');
        $content = strip_tags($content);
        return word_limiter($content, 10, '...');
    }

    /**
     * Called after validation, before saving
     *
     * @param array $data_array associative array made of filtered POST variables
     */
    public function _fb_callback_before_save(&$data_array)
    {
//        // EXAMPLE: Transforming all values to lowercase
//        foreach ($data_array as $key => $value) {
//            $data_array[$key] = strtolower($data_array[$key]);
//        }

        {updated_at_code_element}

        // Computing element slug if one of the slug fields are present


        // List of the fields to be used, if no value is present for a given key
        // then the key will be ignored. By default all values of the keys
        // specified will be concatenated
        $title_field_names = array('name', 'title');

        // List of sluggable field names
        $sluggable_field_names = array('slug', 'url_slug', 'url_name', 'uri_name');

        // Getting form field names
        $field_names = $this->formbuilder->getFieldNames();
        $slug_field = false;
        foreach ($field_names as $field_name) {
            if (in_array($field_name, $sluggable_field_names)) {
                $slug_field = $field_name;
                break; // A single slug field is accepted
            }
        }

        // Only if there is no slug or the slug value is empty
        if ($slug_field) {
            $this->load->helper('string');
            if (!(isset($data_array[$slug_field]) && $data_array[$slug_field])) {
                // Attempt to build a slug
                $slug = '';
                foreach ($title_field_names as $title_field_name) {
                    // Concatenating all the elements
                    if (isset($data_array[$title_field_name]) && $data_array[$title_field_name]) {
                        $slug .= '-' . $data_array[$title_field_name];
                    }
                }

                // Slugify
                $slug = niceuri($slug);

                // Generating pseudo random slug if there was no slug found
                if (!$slug) {
                    $slug = md5(time() . '-some-random-string-34-' . rand(1000, 9999));
                }

                // Assigning the field
                $data_array[$slug_field] = $slug;
            } else {
                // Slugify anyway
                $data_array[$slug_field] = niceuri($data_array[$slug_field]);
            }

            // TODO check if slug is unique

        }
    }

    /**
     * Some logs or statistics maybe?
     * @param array $data_array associative array made of filtered POST variables
     */
    public function _fb_callback_after_save(&$data_array)
    {
        $title = $this->getCompiledTitle('', (object)$data_array);
        Logger::info('Editing element id:' . $this->formbuilder->getId() . ' (' . $title . ')',
            strtoupper(str_replace('Admin', '', __CLASS__)), $this->formbuilder->getId());
    }

    /**
     * Callback function changing the name of the file to SEO friendly
     *
     * @version: 1.2.3
     * @date: 2015-06-11
     *
     * @param $filename
     * @param $base_path
     * @param $data
     * @param $current_image_field_name
     * @return bool
     */
    public function _fb_callback_make_filename_seo_friendly(&$filename, $base_path, &$data, $current_image_field_name)
    {
        // List of the fields to be used, if no value is present for a given key
        // then the key will be ignored. By default all values of the keys
        // specified will be concatenated
        $title_field_names = array('name', 'title', 'label');

        $this->load->helper('string');
        $path = $base_path . $filename;
        $path_parts = pathinfo($path);

        // Attempt to build a name
        $new_base_filename = '';
        foreach ($title_field_names as $title_field_name) {
            // Concatenating all the elements
            if (isset($data[$title_field_name]) && $data[$title_field_name]) {
                $new_base_filename .= '-' . $data[$title_field_name];
            }
        }

        // Making it web safe
        if ($new_base_filename) {
            $new_base_filename = niceuri($new_base_filename);
        }

        // This should not be an else statement as niceuri can return empty string sometimes
        if (!$new_base_filename) {
            $new_base_filename = niceuri($path_parts['filename']);
        }

        // This should normally never happen, but who knows - this is bulletproof
        if (!$new_base_filename) {
            $new_base_filename = md5(time() + rand(1000, 9999));
        }

        $new_base_path = '';
//        $new_base_path = date('Y-m-d') . '/'; // Will create directory based on date
//        $new_base_path = $new_name_base . '/'; // Will create directory based on the niceuri value
//        @mkdir($base_path . $new_base_path); // Do not forget!
        // We don't like upper case extensions
        $extension = strtolower($path_parts['extension']);
        $new_name = $new_base_filename . '.' . $extension;

        // Protection against existing files
        $i = 2;
        while (file_exists($base_path . $new_base_path . $new_name)) {
            $new_name = $new_base_filename . '-' . $i . '.' . $extension;
            if ($i++ > 50 || strlen($i) > 2) // strlen is a protection against the infinity loop for md5 checksums
            {
                // This is ridiculous but who knowss
                $i = md5(time() + rand(1000, 9999));
            }
        }

        // No need to change filename? Then we are fine
        if ($filename == $new_name) {
            return true;
        }

        // Finally here we go!
        if (rename($path, $base_path . $new_base_path . $new_name)) {
            $data[$current_image_field_name] = $new_base_path . $new_name;
            $filename = $new_base_path . $new_name;

            return true;
        }
        return false;
    }

    /**
     * This function is called on delete
     * You should remove any external resources such as images here
     *
     * @param mixed $id
     * @param object $item
     */
    public function _onDelete($id, $item)
    {
        // Get the file or image fields out of the definition
        $file_fields = array();
        $definition = $this->getDefinition();

        // Attempt to find fields contain file paths
        foreach ($definition as $key => $field) {
            if ($field['input_type'] == FormBuilder::IMAGE || $field['input_type'] == FormBuilder::FILE) {
                $file_fields[] = isset($field['field']) && $field['field'] ? $field['field'] : $key;
            }
        }

        // Alternative, static, more safe version
        //$file_fields = array('path', 'image_path', 'attachment_path', 'pdf_path', 'download_path', 'file_path', 'archive_path', 'banner_path', 'baner_path', 'image', 'pdf', 'download', 'file', 'archive', 'baner', 'baner');

        $log_msgs = array();
        foreach ($file_fields as $field) {
            // Checking if the field is set and nonempty
            if (!isset($item->$field) || !trim($item->$field)) {
                continue;
            }

            // Checking whether the file exists
            $path = $this->uploads_base_path . trim($item->$field);
            if (file_exists($path) && is_file($path)) {
                // Trying to delete file
                if (@unlink($path)) {
                    $log_msgs[] = 'unlink ' . $path;
                } else {
                    $log_msgs[] = 'ERROR unlink ' . $path;
                }
            }
        }

        // Deleting translations
//        // Only if the feed object is translateable and the getLocales function exists
//        if (($this->getFeedObject() instanceof Translateable) && is_callable(array($this, 'getLocales'))) {
//            // Generating table name (by convention)
//            $table_name = $this->getFeedObject()->getTable() . '_translations';
//            $this->db->where('object_id', $id)->delete($table_name);
//        }

        // Logging action
        $title = $this->getCompiledTitle('', $item);
        Logger::info('Deleting element id:' . $id . ' (' . $title . ') ' . implode(' ', $log_msgs),
            strtoupper(str_replace('Admin', '', __CLASS__)), $id);
    }


//    /**
//     * Returns a list of locales
//     *
//     * This function should probably be moved out of the controller or should act as a proxy
//     *
//     * @return array
//     */
//    private function getLocales()
//    {
//        // First language is the default one and is not taken into consideration
//        return array('pl_pl', 'en_us');
//    }
//
//    /**
//     * Generic action with redirect
//     */
//    public function generic_redirect_action()
//    {
//        $success = $this->getFeedObject()->doAction();
//
//        $this->load->library('User_agent');
//        $this->load->library('SimpleSessionMessage');
//        if ($success) {
//            $this->simplesessionmessage->setFormattingFunction(SimpleSessionMessage::FUNCTION_SUCCESS);
//            $this->simplesessionmessage->setMessage('global_header_success');
//        }
//
//        // Smart redirect
//        $this->load->library('User_agent');
//        if ($this->agent->referrer()) {
//            redirect($this->agent->referrer());
//        } else {
//            redirect(module_url());
//        }
//    }
//
//    /**
//     * Handles import file upload and parsing
//     *
//     * @param array $data_array
//     * @return boolean
//     */
//    public function _fb_callback_on_import($data_array)
//    {
//        // Overwrite, add own logic here
//        return parent::_fb_callback_on_import($data_array);
//    }
//
//    /**
//     * Link format callback
//     *
//     * @param mixed $content Value of the element
//     * @param object $line Object representing database row
//     * @return string resulting href
//     */
//    public function _crud_action_images($content, $line)
//    {
//        return module_url('images') . 'index' . ($this->input->getParam('layout') ? '/layout-' .
//              $this->input->getParam('layout') : '') . '/forced_filters-' . DataGrid::encodeFiltersString(array('gallery_id' => $line->id));
//    }
//
//    /**
//     * Must populate object
//     *
//     * @param object $object
//     */
//    public function _fb_callback_on_read(&$object)
//    {
//        // If you overwrite this action, you should probably call the model getById action
//        // If you want to keep the original getById it is recommended to use before render callback
//        $object = $this->formbuilder->getFeedObject()->getById($this->formbuilder->getId());
//
//        // There is no object so do not apply translations
//        if (!$object) {
//            return;
//        }
//
//        // Only if the feed object is translateable and the getLocales function exists
//        if (($this->getFeedObject() instanceof Translateable) && is_callable(array($this, 'getLocales'))) {
//            // Getting language list
//            $locales = $this->getLocales();
//            // First language is the default one and is not taken into consideration
//            unset($locales[0]);
//
//            // Generating table name (by convention)
//            $table_name = $this->getFeedObject()->getTable() . '_translations';
//            // Builing an empty structure
//            $translations = array();
//            // Reading all related translations at once
//            $translations_result = $this->db->select('*')->from($table_name)->where('object_id', $this->formbuilder->getId())->get()->result();
//
//            // Walking through translations and building multidimentional structure
//            foreach ($translations_result as $translation_row) {
//                // Create language array if it does not exists
//                if (!isset($translations[$translation_row->locale])) {
//                    $translations[$translation_row->locale] = array();
//                }
//
//                $translations[$translation_row->locale][$translation_row->field] = $translation_row->content;
//            }
//
//            // Assigning variables to flat object
//            foreach ($locales as $locale) {
//                // Walking through translateable fields
//                foreach ($this->getFeedObject()->getTranslateableFieldNames() as $translateable_field) {
//                    $object->{$translateable_field . '_' . $locale} = isset($translations[$locale][$translateable_field]) ? $translations[$locale][$translateable_field] : NULL;
//                }
//            }
//        }
//    }
//
//    /**
//     * Can manipulate data after read, before rendering
//     * @param object $object
//     */
//    public function _fb_callback_before_render(&$object)
//    {
//        // EXAMPLE: Transforming all values to uppercase
//        $attribute_names = array_keys(get_object_vars($object));
//        foreach ($attribute_names as $attribute_name) {
//            $object->$attribute_name = strtoupper($object->$attribute_name);
//        }
//    }
//
//    /**
//     * Must overwrite the save procedure and return true or false
//     * @param array $data_array associative array made of filtered POST variables
//     * @return bool
//     */
//    public function _fb_callback_on_save(&$data_array)
//    {
//        // If you overwrite this action, you should probably call the model saveById action
//        // If you want to keep the original saveById it is recommended to use before save callback along with the after save one
//        $success = $this->formbuilder->getFeedObject()->saveById($this->formbuilder->getId(), $data_array);
//
//        // Skip the rest when it failed to save
//        if (!$success) {
//            return false;
//        }
//
//        // Get last entry id
//        $id = $this->formbuilder->getId();
//        // Form builder assigns ID only after the save callback is executed with success so we need to apply a hack to get the ID out of DB API
//        if (!$id) {
//            $id = $this->db->insert_id();
//        }
//
//        // Only if the feed object is translateable and the getLocales function exists
//        if (($this->getFeedObject() instanceof Translateable) && is_callable(array($this, 'getLocales'))) {
//            // Getting language list
//            $locales = $this->getLocales();
//            // First language is the default one and is not taken into consideration
//            unset($locales[0]);
//
//            // Copying fields for each language
//            $data_array_translated = array();
//            foreach ($locales as $locale) {
//                $data_array_translated[$locale] = array();
//
//                foreach ($this->getFeedObject()->getTranslateableFieldNames() as $translateable_field) {
//                    $data_array_translated[$locale][$translateable_field] = isset($data_array[$translateable_field . '_' . $locale]) ? $data_array[$translateable_field . '_' . $locale] : NULL;
//                }
//            }
//
//            // Generating table name (by convention)
//            $table_name = $this->getFeedObject()->getTable() . '_translations';
//            foreach ($locales as $locale) {
//                foreach ($data_array_translated[$locale] as $field => $content) {
//                    // Check if the entry already exists in the database
//                    if ($this->db->where('object_id', $id)->where('locale', $locale)->where('field', $field)->count_all_results($table_name) > 0) {
//                        // Update existing row
//                        $this->db->where('object_id', $id)->where('locale', $locale)->where('field', $field)->set('content', $content)->update($table_name);
//                    } else {
//                        // Insert a new row
//                        $this->db->set('object_id', $id)->set('locale', $locale)->set('field', $field)->set('content', $content)->insert($table_name);
//                    }
//                }
//            }
//        }
//
//        return $success;
//    }
//
//    /**
//     * Put here your rollback action
//     *
//     * @param array $data_array associative array made of filtered POST variables
//     */
//    public function _fb_callback_on_save_failure(&$data_array)
//    {
//        // Rollback
//    }
//
//
//    /**
//     * Image format callback
//     *
//     * @param string $content
//     * @param object $line
//     * @return string
//     */
//    public function _datagrid_format_image_field($content, $line)
//    {
//        return $this->uploads_base_path . $line->image_path;
//    }
}
