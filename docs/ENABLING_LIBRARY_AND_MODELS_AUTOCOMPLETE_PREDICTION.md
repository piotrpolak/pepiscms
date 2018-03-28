# Enabling library and models autocomplete prediction

Autocomplete and method prediction works out of the box for classes that extend either
[EnhancedController](../../../tree/master/pepiscms/application/classes/EnhancedController.php)
(all PepisCMS controller types), [Generic_model](../../../tree/master/pepiscms/application/models/Generic_model.php)
and [ContainerAware](../../../tree/master/pepiscms/application/classes/ContainerAware.php).

This is obtained by adding `@property` annotations to the above mentioned classes.

# Generating project headers manually

To regenerate libraries and models definition and enable autocomplete predictions for CodeIgniter in PepisCMS you need to:

1. Install [Development tools](MODULES.md#development-tools) module and navigate to the module's dashboard
    ![Autocomplete](screens/ENABLING_LIBRARY_AND_MODELS_AUTOCOMPLETE_PREDICTION_1.png)

3. Generate *headers* file, the action will generate a definition file located under `application/dev/_project_headers.php`
    ![_project_headers.php](screens/ENABLING_LIBRARY_AND_MODELS_AUTOCOMPLETE_PREDICTION_2.png)
    
4. Mark CodeIgniter Controller.php and Model.php as text (`Right click -> Mark as plaintext`).
    The files paths are `vendor/codeigniter/framework/system/core/Controller.php`
    and `vendor/codeigniter/framework/system/core/Model.php` respectively.
    
5. Benefit from autocomplete predictions and code suggestions :)
    ![Autocomplete](screens/ENABLING_LIBRARY_AND_MODELS_AUTOCOMPLETE_PREDICTION_3.png)