# Features


* **Modularity**

    Modularity and consistency. An external modules can be written independent on the system core.
    System core can be upgraded without any modification in the current application at any time.
  
    There are two kinds of modules - builtin (available in all projects) and user-space modules.
    Modules can be enabled or disabled from the administration panel.
    A typical module consists from both admin and public controllers and support code.
    
    Read more about [modules](MODULES.md).
  
* **Advanced user and user right management**

    The user is granted a certain right above an entity.
    Every single controller method has associated a minimal right above a certain entity.
    
    All violations of security policy are reported in system audit logs.
    
    You can create as many users as you want, you can assign a user to several groups, the security policy can
    be modified at runtime.
    
    Read more about [security policy](SECURITY_POLICY.md).
  
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
    An integrated [translator](MODULES.md#translator) speeds up multi language application development.
  
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
    The [data grid](LIBRARY_DATAGRID.md) supports ordering table by any column and implements multiple filters.
    The [form generator](LIBRARY_FORMBUILDER.md) implements validation, data retrieval and data save,
    file upload and many others by default.
    
    You can extend or overwrite behavior of these components using [advanced callbacks](LIBRARY_FORMBUILDER.md#lifecycle-callbacks).
  
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