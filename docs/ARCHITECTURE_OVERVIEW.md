# Architecture overview

## Core of the system

Core implements most of the base features and it is supposed to be stable over the time.
The core takes advantage of [CodeIgniter framework](https://codeigniter.com/) that provides basic architecture,
input security and database ActiveRecord access method.

Specifically for the core of PepisCMS some of the classes were overloaded or rewritten
(see [overwritten core libraries](../../../tree/master/pepiscms/application/core/).

PepisCMS distinguishes between 4 types of controllers:

* **Basic controller** (same as for CodeIgniter)
* **[ModuleController](../../../tree/master/pepiscms/application/classes/ModuleController.php)** - public module
    controller
* **[AdminController](../../../tree/master/pepiscms/application/classes/AdminController.php)** - for core administration
    panel, handles security check transparently
* **[ModuleAdminController](../../../tree/master/pepiscms/application/classes/ModuleAdminController.php)** - module
    administration panel controller that supports "hotplug" and a kind of visualization where the original
    AdminController acts as a host and maps all accessible resources (translates) to ModuleAdminController instance
    without duplicating them in memory

## Modules

Modules implement business specific features that are not generic and should not be
distributed for all the instances of the application. Modules can be easily transferred from a
project to project and should not create dependency conflicts. Every single module can be
enabled/disabled at any time.

System and user modules they work exactly the same. If an user space module of the same
name as an existing system module is installed, the system module will be completely ignored –
you can overwrite the default users and logs modules.

Read more about [Modules](MODULES.md).

## Model-View-Presenter architectural pattern

The application is built around [Model View Presenter pattern](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93presenter)).

A request triggers a presenter (controller) method that uses logic encapsulated in models (and libraries) to prepare
data for the view layer.

### Model 

Model encapsulates data access using the [Data Access Object](https://en.wikipedia.org/wiki/Data_access_object) pattern.

Models provide methods that read, update and delete entities. CodeIgniter uses instances of stdClass to represent entities.

Entities:

* have no methods
* have fields that reflect database structure (query result set structure)

All models must start with a capital letter and must be suffixed with _model. Unlike libraries,
the model instance names are case sensitive (this comes from CodeIgniter engine). 

Read more about [CodeIgniter models](https://www.codeigniter.com/user_guide/general/models.html).

PepisCMS provides some base models that can be extended in order make development simpler:

| Name                                                                            | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
|---------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [Generic_model](GENERIC_MODEL.md)                                               | Generic model is the improved version od CodeIgniter model. It implements methods specified by both [EntitableInterface](../../../tree/master/pepiscms/application/classes/EntitableInterface.php) and [AdvancedDataFeedableInterface](../../../tree/master/pepiscms/application/classes/AdvancedDataFeedableInterface.php) interfaces. In most cases this class should be extended and parametrized in the constructor but it is left as a non-abstract for DataGrid and FormBuilder components that initialize and parametrize Generic_model "on-the-fly" using prototype design pattern. |
| [Array_model](../../../tree/master/pepiscms/application/models/Array_model.php) | Provides Generic_model capabilities that can be applied to data sources other than database.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| [Ssh_model](../../../tree/master/pepiscms/application/models/Ssh_model.php)     | Provides Generic_model and Array_model capabilities for data parsed over SSH                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |

### View 

View is used as the presentation layer. It is fully controlled by controller but it can also pull some data provided by
models or libraries initialized by controller. It is a good technique to prepare all the data inside the controller and
to use view only as the "display layer" with absolutely no logic inside - doing so makes it much easier to change
the view, for example from pure HTML to JSON format used by JavaScript.

To load a view you should use the `$this->display()` method from the `Enhanced_controller`. It will automatically compute
view's path based on the module name (if any), controller name and method name.

To assign a variable to the view, you should use the `$this->assign('variable-name', 'variable-value`)` method.

### Controller

Controllers are these components that interpret input, take some decisions and prepare data for the output.
In the case of web applications by input we understand GET/POST variables, Session variables Cookies and several others.
Controller implements some of the logic and pulls data from different locations – database, web services, APIs, sockets etc. 

Controllers take advantage of libraries and models they load, they should not manipulate the database directly but by using models. 

PepisCMS distinguishes 4 types of controllers:

| Name                                                                                                                          | Description                                                                                                         |
|-------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------|
| [EnhancedController](../../../tree/master/pepiscms/application/classes/EnhancedController.php) extends Controller (abstract)  | Implements handy methods like assign($name, $variable) that are used in any other type of controllers.              |
| [AdminController](../../../tree/master/pepiscms/application/classes/AdminController.php) extends EnhancedController           | Used for administration panel only. Authorization and authentication is implemented in the constructor.             |
| [ModuleAdminController](../../../tree/master/pepiscms/application/classes/ModuleAdminController.php) extends AdminController  | Similar to the AdminController, used for back-end controllers of modules.                                           |
| [ModuleController](../../../tree/master/pepiscms/application/classes/ModuleController.php) extends EnhancedController         | Used for front-end controllers of modules. Use document library to set page titles and to interact with the system  |
