# Model-View-Controller architectural pattern

The application will be built around MVC pattern (Model View Controller). The basing idea behind MVC is that the presentation layer (view) is completely isolated from the business logic layer (controller) and the domain-specific representation of the information on which the application operates (model). 
MVC results high flexibility, by modifying models, we can adopt the application to any data source, for example we can story all the contents in the raw txt files or XML file. 

## Model 

Model represents application logic. In context of web and PepisCMS model can be interpreted as the database access and manipulation layer. Models' code must be kept as simple as possible and should not refer to external resources or server input. Doing so makes it easy to reuse models for different purposes than a simple website. 
When using PepisCMS you should think of models as set of methods that operate on entities. A model itself is not an entity. Entities are represented with instances of dedicated classes or stdClass (parent class of all classes in PHP) that have no methods and all its attributes are public. Attributes usually represent the database structure but this is not a rule. You can load a model as many times you want; models are initialized as singleton for performance reasons. 
All models must start with a capital letter and must be suffixed with _model. Unlike libraries, the model instance names are case sensitive (this comes from CodeIgniter engine). 

## View 

View is used as the presentation layer. It is fully controlled by controller but it can also pull some data provided by models or libraries initialized by controller. It is a good technique to prepare all the data inside the controller and to use view only as the "display layer" with absolutely no logic inside – doing so makes it much easier to change the view, for example from pure HTML to JSON format used by JavaScript. 

## Controller

Controllers are these components that interpret input, take some decisions and prepare data for the output. In the case of web applications by input we understand GET/POST variables, Session variables Cookies and several others. Controller implements some of the logic and pulls data from different locations – database, web services, APIs, sockets etc. 
Controllers take advantage of libraries and models they load. Controller should not manipulate the database directly but by using models. 

PepisCMS uses 4-5 types of controllers:


| Name                                              | Description                                                                                                         |
|---------------------------------------------------|---------------------------------------------------------------------------------------------------------------------|
| EnhancedController extends Controller (abstract)  | Implements handy methods like assign($name, $variable) that are used in any other type of controllers.              |
| AdminController extends EnhancedController        | Used for administration panel only. Authorization and authentication is implemented in the constructor.             |
| ModuleAdminController extends AdminController     | Similar to the above controller, used for back-end controllers of modules.                                          |
| ModuleController extends EnhancedController       | Used for front-end controllers of modules. Use document library to set page titles and to interact with the system  |
| Widget                                            | Similar to any other controller but must return a string.                                                           |
| WebserviceController                              | Defines methods that can be remotely called. It is more like a model than a service.                                |
