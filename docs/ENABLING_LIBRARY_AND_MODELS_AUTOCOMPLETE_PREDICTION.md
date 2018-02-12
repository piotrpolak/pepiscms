# Enabling library and models autocomplete prediction

Since CodeIgniter dynamically registers libraries and models in a controller, by default you can't benefit from
IDE autocomplete predictions and method suggestions.

To enable autocomplete predictions for CodeIgniter in PepisCMS you need to:

1. Install [Development tools](MODULES.md#development-tools) module
2. Go to *Development tools* module
3. Generate *headers* file, the action will generate a definition file located under `application/dev/_project_headers.php`
    ![_project_headers.php](screens/ENABLING_LIBRARY_AND_MODELS_AUTOCOMPLETE_PREDICTION_1.png)
    
4. Disable scanning CodeIgniter and PepisCMS provided Controller (`vendor/codeigniter/framework/system/core/Controller.php`)
    and `Model` (`vendor/codeigniter/framework/system/core/Model.php`)  by marking the files as text
    (`Right click -> Mark as plaintext`)
5. Benefit from autocomplete predictions and code suggestions :)
    ![Autocomplete](screens/ENABLING_LIBRARY_AND_MODELS_AUTOCOMPLETE_PREDICTION_2.png)