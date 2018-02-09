# PepisCMS

A legacy project, cleaned up and released as open source on its 10th birthday.

It is written in PHP on top of CodeIgniter framework.

As 2017, the project is fully functional (and really fast) but you should be aware that it is mostly made up from
**legacy code**.

For most of the time the code was fully PHP 5.2 compatible. Currently, as CodeIgniter dropped support for PHP 5.2, the
minimum version is PHP 5.3.

* [Installation](docs/INSTALLATION.md)
* [Modules](docs/MODULES.md)
* [Generating a CRUD module](docs/GENERATING_A_CRUD_MODULE.md)
* [Security policy](docs/SECURITY_POLICY.md)
* [Benchmarking](docs/BENCHMARKING.md)
* [Naming convention inconsistency](docs/NAMING_CONVENTION_INCONSISTENCY.md)
* [Deployment configurations](docs/DEPLOYMENT_CONFIGURATIONS.md)
* [Simplified domain model](docs/SIMPLIFIED_DOMAIN_MODEL.md)
* [Architecture overview](docs/ARCHITECTURE_OVERVIEW.md)
* Core libraries:
    * [FormBuilder](docs/LIBRARY_FORMBUILDER.md)
    * [DataGrid](docs/LIBRARY_DATAGRID.md)
    * [CrudDefinitionBuilder](docs/LIBRARY_CRUD_DEFINITION_BUILDER.md)
* [Breaking API changes and upgrade instructions](CHANGES.md)
## Features

* **Modularity**

    Modulability and consistency. An external modules can be written independent on the system core.
    System core can be upgraded without any modification in the current application at any time.
  
    There are two kinds of modules - builtin (available in all projects) and project-space modules.
    Modules can be enabled or disabled from the admin panel. A typical module consists from both admin and public
    contollers and support code.
  
* **Advanced user and user right management**

    The user is granted a certain right above an entity.
    Every single method has associated a minimal right above a certain entity. All violations of security policy are
    reported. You can create as many users as you want, you can assign a user to several groups, the security policy can
    be modified at runtime.
  
* **Audit logs**
  
    All user actions and unexpected application behaviors can be tracked using an advanced log utility.
    PepisCMS provides a logging API and a console for analyzing system logs.
  
* **User session security**

    User session will expire after one hour of inactivity and the system will ask the user to authenticate again.
    The session is protected against session spoofing attack by validating the IP each time the system validates
    the user rights.
  
* **Web based file manager**

    The user can manage files on the server using a lightweight AJAX file manager.
    You can restrict the allowed upload extension list.
  
* **Enhanced SMTP Email sender**
  
    An utility for reliable sending emails. When the system is unable to connect to the remote SMTP server, an
    alternative gateway is used and the action is reported.
  
* **Multi language native support** 

    The application supports internationalization and multi language support by default, both for front-end and backend.
    An integrated translator using Google Translate API speeds up multi language application development.
  
* **Rich Text Editor**

    Makes you you feel you are using MS Word while editing web site contents.
    You can change text formatting as well as attach pictures.

* **Configuration tests on startup**

    Basic configuration tests ensure system cohesion and proper operation on any environment.
  
* **Two level cache mechanism**

    Makes your web site bulletproof. The HTML output cache mechanism is run before the framework is initialized and
    serves static contents at the speed of serving static pages.
  
* **Backup utility**

    Create and restore web site contents from XML backup, create SQL dumps.
  
* **Intranet options**

    You can block access to front-end contents as well as to uploaded files for unauthenticated users.
  
* **SEO Friendly**

    PepisCMS generates SEO friendly links and optimized meta tags. It also automatically generates sitemap
    (both txt and xml) for any web site.
  
* **Build-in components for generating grids and forms.**
  
    Using these components you have 90% of requested functionalities implemented at start.
    The data grid supports ordering table by any collumn and implements multiple filters.
    The form generator implements validation, data retrieval and data save, file upload and many others by default.
    You can extend or overwrite behavior of these components using advanced callbacks.
  
* **Build-in helpers for generating Excel files and PDF files.**

    You can easily export application data into XLS file or print any HTML to PDF.
  
* **Content journaling**
 
    Makes it simple to track down changes upon entities and to restore specific values.
  
* **CRUD module generator**

    Generate database CRUD admin panel in minutes.
    
* **Builtin UI translator**

    Localize your application with no effort.
  
* **Builtin SQL console**
  
    Makes maintenance and upgrade tasks a piece of cake.

* **Seamless Symfony2 integration**
 
    Consume Symfony2 encapsulated business logic inside CMS panel. Benefit from the powerful dependency injection
    engine.
  
* **Admin panel customization**

    Makes it possible to customize the look of administration panel by offering the possibility of injecting
    HTML/JS/CSS code in selected sections of the view.
  
* **Backward compatibility**

    Any instance of PepisCMS can be easily upgraded to a newer version with minimal effort.
    All versions within the same branch are 100% backward compatible.
  
* **Cache and admin panel speed**

    Critical components of administration panel are cached thus improving overall performance of CMS.
  
* **Twig integration**

* **SSH and Array models**

    Makes it possible to operate on models that provide data out of any source.
  
* **Google Chart integration**

* **phpCAS/native authentication drivers**

## Running tests

```bash
docker-compose up
```

and then

### Unit tests

```bash
docker exec -it pepiscms_web_1 vendor/bin/behat
```

### Smoke tests (behat)

```bash
docker exec -it pepiscms_web_1 vendor/bin/behat
```

## Docker cleanup

```bash
docker-compose rm --stop
```

## Using PepisCMS command line commands

```bash
php index.php tools index
```

## Checking code syntax

```bash
./check-code.sh
```

## Optimizing documentation images

```bash
optipng *.png
```