# CrudDefinitionBuilder library

CrudDefinitionBuilder makes it easier and safer (typesafe) to generate DataGrid and FormBuilder definitions.

Below some sample code:

```php
$definition = CrudDefinitionBuilder::create()
    ->withField('address')
        ->withFilterType(DataGrid::FILTER_BASIC)
        ->withShowInGrid(FALSE)
        ->withShowInForm(TRUE)
        ->withInputType(FormBuilder::TEXTFIELD)
        ->addValidationRule('required')
        ->addValidationRule('valid_phone_number')
        ->addValidationRule('max_length[13]')
    ->end()
    ->withField('date')
        ->withShowInGrid(TRUE)
        ->withShowInForm(FALSE)
        ->withInputType(FormBuilder::TEXTFIELD)
    ->end()
        ->withField('date_sent')
        ->withShowInGrid(TRUE)
        ->withShowInForm(FALSE)
        ->withInputType(FormBuilder::TEXTFIELD)
    ->end()
    ->withField('body')
        ->withFilterType(DataGrid::FILTER_BASIC)
        ->withShowInGrid(TRUE)
        ->withShowInForm(TRUE)
        ->withInputType(FormBuilder::TEXTAREA)
        ->addValidationRule('required')
        ->addValidationRule('max_length[480]')
    ->end()
    ->withField('is_incoming')
        ->withFilterType(DataGrid::FILTER_SELECT)
        ->withValues(array(
            0 => $this->lang->line('global_dialog_no'),
            1 => $this->lang->line('global_dialog_yes')
        ))
        ->withFilterValues(array(
            0 => $this->lang->line('global_dialog_no'),
            1 => $this->lang->line('global_dialog_yes')
        ))
        ->withShowInGrid(TRUE)
        ->withShowInForm(FALSE)
        ->withInputType(FormBuilder::TEXTAREA)
        ->withNoValidationRules()
    ->end()
    ->withImplicitTranslations($module_name, $this->lang)
    ->build();
```