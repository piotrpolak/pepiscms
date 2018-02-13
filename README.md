# PepisCMS

[![Build Status](https://travis-ci.org/piotrpolak/pepiscms.svg?branch=master)](https://travis-ci.org/piotrpolak/pepiscms)
[![Maintainability](https://api.codeclimate.com/v1/badges/63fd33946e2cd355a561/maintainability)](https://codeclimate.com/github/piotrpolak/pepiscms/maintainability)

PepisCMS is a content management system. Its main feature is the [CRUD module generator](docs/GENERATING_A_CRUD_MODULE.md)
which makes it easy to set up an entire administration panel based on the database schema definition within minutes.
The generated administration panel consists of [modules](docs/MODULES.md) that can be customized (or just left as they
are if you don't care about details).

* [Installation](docs/INSTALLATION.md)
* [Modules](docs/MODULES.md)
* [Generating a CRUD module](docs/GENERATING_A_CRUD_MODULE.md)
* [Security policy](docs/SECURITY_POLICY.md)
* [Deployment configurations](docs/DEPLOYMENT_CONFIGURATIONS.md)
* [Simplified domain model](docs/SIMPLIFIED_DOMAIN_MODEL.md)
* [Architecture overview](docs/ARCHITECTURE_OVERVIEW.md)
* Core libraries:
    * [Generic model](docs/GENERIC_MODEL.md)
    * [FormBuilder](docs/LIBRARY_FORMBUILDER.md)
    * [DataGrid](docs/LIBRARY_DATAGRID.md)
    * [CrudDefinitionBuilder](docs/LIBRARY_CRUD_DEFINITION_BUILDER.md)
* [Breaking API changes and upgrade instructions](CHANGES.md)
* [Benchmarking](docs/BENCHMARKING.md)
* [Naming convention inconsistency](docs/NAMING_CONVENTION_INCONSISTENCY.md)
* [Changes comparing to CodeIgniter](docs/CHANGES_COMPARING_TO_CODEIGNITER.md)
* [Enabling library and models autocomplete predictions](docs/ENABLING_LIBRARY_AND_MODELS_AUTOCOMPLETE_PREDICTION.md)

## Some history

PepisCMS was started in 2007 as an experimental academic project.
It is written on top of the [CodeIgniter framework](https://codeigniter.com/) and during its lifetime it has been
fluently ported from CodeIgniter version 1.5.4 to 3.0 (and counting).

As 2018, the project is **fully functional** (and really fast) but you should be aware that its source code is quite far
away from php latest architectural styles and by some it might be considered a **legacy** (and that would not be offensive,
see its [maintainability score](https://api.codeclimate.com/v1/badges/63fd33946e2cd355a561/maintainability) at Code Climate).

## Development philosophy

During all those years the project has been developed using a very conservative approach and manually tested on multiple
deployments prior to releasing a stable version. This made it possible to keep regression to minimum.

### Becoming open source

On its "10th birthday" the project was released as open source under the [MIT license](LICENSE.txt).

Prior to pushing the project to github its code has been slightly refactored, cleaned up from any proprietary code,
described by some tests and released as a composer dependency.

Being a composer module PepisCMS now benefits from the component management. Upgrading any of its dependencies is now
simplified to incrementing composer versions.

All of the above makes it easy to provide hot fixes and components' updates thus it extends the expected lifespan of the
product.

## Features

* **Modularity**

    Modularity and consistency. An external modules can be written independent on the system core.
    System core can be upgraded without any modification in the current application at any time.
  
    There are two kinds of modules - builtin (available in all projects) and user-space modules.
    Modules can be enabled or disabled from the administration panel.
    A typical module consists from both admin and public controllers and support code.
    
    Read more about [modules](docs/MODULES.md).
  
* **Advanced user and user right management**

    The user is granted a certain right above an entity.
    Every single controller method has associated a minimal right above a certain entity.
    
    All violations of security policy are reported in system audit logs.
    
    You can create as many users as you want, you can assign a user to several groups, the security policy can
    be modified at runtime.
    
    Read more about [security policy](docs/SECURITY_POLICY.md).
  
* **Audit logs**
  
    All user actions and unexpected application behaviors can be tracked down using an advanced log utility.
    PepisCMS provides a logging API and a console for analyzing system logs.
  
* **User session security**

    User session will expire after one hour of inactivity (configurable).
    The session is protected against session spoofing attack by validating the IP each time the system validates
    the user rights.
  
* **Web based file manager**

    The user can manage files on the server using a lightweight AJAX file manager.
    You can restrict the allowed upload extension list.
  
* **Enhanced SMTP Email sender**
  
    An utility for sending emails in a reliable way reliable.
    When the system is unable to connect to the remote SMTP server, an alternative gateway is used
    and the fallback action is reported in audit logs.
  
* **Multi language native support** 

    The application supports internationalization and multi language support by default, both for front-end and backend.
    An integrated [translator](docs/MODULES.md#translator) speeds up multi language application development.
  
* **Rich Text Editor**

    Makes you you feel you are using MS Word while editing web site contents.
    You can change text formatting and attach pictures.

* **Configuration tests on startup**

    Basic configuration tests ensure system cohesion and proper operation on any environment.
  
* **Two level cache mechanism**

    Makes your web site bulletproof. The HTML output cache mechanism is run before the framework is initialized and
    serves static contents at the speed of serving static pages.
  
* **Backup utility**

    Create and restore web site contents from XML backup, create SQL dumps.
  
* **Intranet options**

    You can restrict access to public contents and uploaded files for unauthenticated users.
  
* **SEO Friendly**

    PepisCMS generates SEO friendly links and optimized meta tags. It also automatically generates sitemap
    (both txt and xml) for the website.
  
* **Build-in components for generating grids and forms.**
  
    Using these components you have 90% of requested features implemented from the start.
    The [data grid](docs/LIBRARY_DATAGRID.md) supports ordering table by any column and implements multiple filters.
    The [form generator](docs/LIBRARY_FORMBUILDER.md) implements validation, data retrieval and data save,
    file upload and many others by default.
    
    You can extend or overwrite behavior of these components using [advanced callbacks](docs/LIBRARY_FORMBUILDER.md#lifecycle-callbacks).
  
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

**Unit tests**

```bash
docker exec -it pepiscms_web_1 ./vendor/bin/phpunit -c ./vendor/piotrpolak/pepiscms/
```

**Smoke tests (behat)**

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