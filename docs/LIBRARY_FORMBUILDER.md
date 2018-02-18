# FormBuilder library

A library that can build and handle HTML forms based on the provided definition.
Form builder can render the form using the default layout or you can register an additional renderer.

The main features of form builder are:

* Generates HTML forms from definition 
* Handles input validation, both on server side (for security) and on client side (JavaScript for increased
    responsiveness) 
* Handles database read/update/insert 
* Can be customized using templates 
* Can be extended using callbacks and custom feed objects that implement the
    [EntitableInterface](../../../tree/master/pepiscms/application/classes/EntitableInterface.php)
* Can handle database foreign keys 
* When using FormBuilder you can specify the fields of the form and their attributes in two manners: by API methods or
    by definition. The API methods were first that were developed but they are not recommended to use in new projects.
    Initializing the form from definition that is an associative array (multidimensional hash table) makes it much more
    flexible and reusable – when new attributes are implemented, the previously defined forms work with no problems.
    It is also simpler for the programmer because the order of attributes in the definition does not matter. If there is
    no attribute specified its default value is automatically completed. 
* The definition used for generating FormBuilder is compatible with the definition used by DataGrid so that one
    definition can be written and reused both for the Form and for the Grid. 
* Form builder is closely coupled with
    [EntitableInterface](../../../tree/master/pepiscms/application/classes/EntitableInterface.php) - it uses its
    `saveById()` and `getById()` methods.

See complete [FormBuilder API](../../../tree/master/pepiscms/application/libraries/FormBuilder.php)

## Generating a form 

In the most common scenario the customizable Generic_model is used as the feed object for FormBuilder. The scenario
looks as follows: 

1. Initialize FormBuilder `$this->load->library('FormBuilder')`
2. Specify data model or use GenericModel providing the table name
3. Specify the value of entity ID - `$this->formbuilder->setId($id)` 
4. Specify the fields and their properties by `$this->formbuilder->setDefinition($definition)` 
5. Specify the back link `$this->formbuilder->setBackLink($link)` - URL that is used for the "Cancel" button and for
    redirecting the user once the form is saved 
6. Trigger form populate/save actions and generate the resulting HTML 


## Lifecycle callbacks 

Callbacks are used to extend or to overwrite certain operations done by FormBuilder. 

READ related callbacks should take the OBJECT as the parameter while the WRITE callbacks should take ARRAY as parameter.
The reason for that is that rows are retrieved from database as objects while the form values come as associative array. 

If you are not fine with that, you can ensure a certain type by type casting: 
`$object = (object) $array` and PHP will do all the magic. 

Callbacks are usually defined in controller code as methods prefixed with "_"to prevent it from being accessed via HTTP.
They can be delegated to external classes or can be simple functions as long as they are callable with the specified
parameters. The concept of the callback is very similar to the concept of the ActionListener. Please note that if you
define the callback in the controller class it must be public so that it can be called from an an external instance. 


| Method name               | Description                                                                                                                                                                                                                                                                                                                                                  |
|---------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| CALLBACK_BEFORE_RENDER    | Called after retrieving the data but before rendering the form. The callback function takes must take the OBJECT parameter as reference.                                                                                                                                                                                                    |
| CALLBACK_BEFORE_SAVE      | Called before saving the data. The callback function must take the ARRAY parameter as reference.                                                                                                                                                                                                                                            |
| CALLBACK_AFTER_SAVE       | Called after saving the data. The callback function must take the ARRAY parameter as reference.                                                                                                                                                                                                                                             |
| CALLBACK_ON_SAVE_FAILURE  | Called when data save fails. It can be using for rollback operations.The callback function takes must take the ARRAY parameter as reference.                                                                                                                                                                                                |
| CALLBACK_ON_SAVE          | Called on save. This kind of callback should be used when no feed object specified of when you want to overwrite the default SAVE operation. The callback function takes must take the ARRAY parameter as reference and MUST return TRUE or FALSE. If the function returns FALSE, it should also set FormBuilder validation error message.  |
| CALLBACK_ON_READ          | Called on read. This kind of callback should be used when no feed object specified of when you want to overwrite the default READ operation.The callback function takes must take the OBJECT parameter as reference and to FILL it.The callback does not need to return anything.                                                           |


Callback setup:
```php
$this->formbuilder->setCallback( array($this, '_fb_callback_before_render'), FormBuilder::CALLBACK_BEFORE_RENDER ); 
$this->formbuilder->setCallback( array($this, '_fb_callback_before_save'), FormBuilder::CALLBACK_BEFORE_SAVE ); 
$this->formbuilder->setCallback( array($this, '_fb_callback_after_save'), FormBuilder::CALLBACK_AFTER_SAVE ); 
$this->formbuilder->setCallback( array($this, '_fb_callback_on_save'), FormBuilder::CALLBACK_ON_SAVE ); 
$this->formbuilder->setCallback( array($this, '_fb_callback_on_save_failure'), FormBuilder::CALLBACK_ON_SAVE_FAILURE ); 
$this->formbuilder->setCallback( array($this, '_fb_callback_on_read'), FormBuilder::CALLBACK_ON_READ ); 
 
/** 
 * Called after validation, before saving 
 * @param array $data_array 
 */ 
public function _fb_callback_before_save( &$data_array ){} 
 
/** 
 * Some logs or statistics maybe? 
 * @param array $data_array  
 */ 
public function _fb_callback_after_save( &$data_array ){} 
 
/** 
 * Put here your rollback action 
 * @param object $object  
 */ 
public function _fb_callback_on_save_failure( &$object ){} 
 
/** 
 * Must overwrite the save procedure and return true or false 
 * @param object $object  
 */ 
public function _fb_callback_on_save( &$object ){} 
 
/** 
 * Must populate object 
 * @param object $object  
 */ 
public function _fb_callback_on_read( &$object ){} 
 
/** 
 * Can manipulate data after read, before rendering 
 * @param object $object  
 */ 
public function _fb_callback_before_render( &$object ){} 
```

## Image fields’ callbacks

You can attach an independent callback to the image fields. The difference between form builder general callbacks and
image callbacks is that image callback is only called when a new image is being uploaded while form builder callback
is called each time you save a form. 

Sample callback (taken from admin module CRUD template):
```php
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
        return TRUE;
    }

    // Finally here we go!
    if (rename($path, $base_path . $new_base_path . $new_name)) {
        $data[$current_image_field_name] = $new_base_path . $new_name;
        $filename = $new_base_path . $new_name;

        return TRUE;
    }
    return FALSE;
}
```