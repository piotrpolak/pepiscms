# Architecture overview

## Core of the system

Core implements most of the base features and it is supposed to be stable over the time.
The core takes advantage of CodeIgniter framework that provides basic architecture, input security and database
ActiveRecord access method.

Specifically for the core of PepisCMS some of the classes were overloaded or rewritten
(see [overwritten core libraries](pepiscms/application/core/).

PepisCMS distinguishes between 4 types of controllers:


* **Basic controller** (same as for CodeIgniter)
* **ModuleController** - public module controller
* **AdminController** - for core administration panel, handles security check transparently
* **ModuleAdminController** - module administration panel controller that supports "hotplug" and a kind of
visualization where the original AdminController acts as a host and maps all accessible resources (translates)
to ModuleAdminController instance without duplicating them in memory

## Modules

Modules implement business specific features that are not generic and should not be
distributed for all the instances of the application. Modules can be easily transferred from a
project to project and should not create dependency conflicts. Every single module can be
enabled/disabled at any time.

System and user modules they work exactly the same. If an user space module of the same
name as an existing system module is installed, the system module will be completely ignored –
you can overwrite the default users and logs modules.

Read more about [Modules](MODULES.md).

## Model-View-Controller architectural pattern

The application will be built around MVC pattern (Model View Controller). The basing idea behind MVC is that the
presentation layer (view) is completely isolated from the business logic layer (controller) and the domain-specific
representation of the information on which the application operates (model). 
MVC results high flexibility, by modifying models, we can adopt the application to any data source,
for example we can story all the contents in the raw txt files or XML file. 

### Model 

Model represents application logic. In context of web and PepisCMS model can be interpreted as the database access and
manipulation layer. Models' code must be kept as simple as possible and should not refer to external resources
or server input. Doing so makes it easy to reuse models for different purposes than a simple website. 
When using PepisCMS you should think of models as set of methods that operate on entities.

A model itself is not an entity - entities are represented with instances of dedicated classes or stdClass
(parent class of all classes in PHP) that have no methods and all its attributes are public.
Attributes usually represent the database structure but this is not a rule.
You can load a model as many times you want; models are initialized as singleton for performance reasons. 
All models must start with a capital letter and must be suffixed with _model. Unlike libraries,
the model instance names are case sensitive (this comes from CodeIgniter engine). 

Read more about [CodeIgniter models](https://www.codeigniter.com/user_guide/general/models.html).

| Name                                                                          | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
|-------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [Generic_model](pepiscms/application/models/Generic_model.php))               | Generic model is the improved version od CodeIgniter model. It implements methods specified by both [EntitableInterface](pepiscms/application/classes/EntitableInterface.php) and [AdvancedDataFeedableInterface](pepiscms/application/classes/AdvancedDataFeedableInterface.php) interfaces. In most cases this class should be extended and parametrized in the constructor but it is left as a non-abstract for DataGrid and FormBuilder components that initialize and parametrize Generic_model "on-the-fly" using prototype design pattern. |
| [Array_model](pepiscms/application/models/Array_model.php)                    | Provides Generic_model capabilities that can be applied to data sources other than database.                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| [Ssh_model](pepiscms/application/models/Ssh_model.php)                        | Provides Generic_model and Array_model capabilities for data parsed over SSH                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| [Remote_model](pepiscms/application/models/Remote_model.php)                  | Defines methods that can be remotely called. It is more like a model than a service.                                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| [RemoteConsumer_model](pepiscms/application/classes/RemoteConsumer_model.php) | Defines methods that can be remotely called. It is more like a model than a service.                                                                                                                                                                                                                                                                                                                                                                                                                                                              |

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


| Name                                                                                                     | Description                                                                                                         |
|----------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------|
| [EnhancedController](pepiscms/application/classes/EnhancedController.php) extends Controller (abstract)  | Implements handy methods like assign($name, $variable) that are used in any other type of controllers.              |
| [AdminController](pepiscms/application/classes/AdminController.php) extends EnhancedController           | Used for administration panel only. Authorization and authentication is implemented in the constructor.             |
| [ModuleAdminController](pepiscms/application/classes/ModuleAdminController.php) extends AdminController  | Similar to the AdminController, used for back-end controllers of modules.                                           |
| [ModuleController](pepiscms/application/classes/ModuleController.php) extends EnhancedController         | Used for front-end controllers of modules. Use document library to set page titles and to interact with the system  |
