# ContainerAware

[ContainerAware](../../../tree/master/pepiscms/application/classes/ContainerAware.php) is a way to access CodeIgniter
container services in a seamless way from your libraries and custom classes.

All you need to do is to make your class extend ContainerAware and you can then immediately access all services just
like you access them from within the controllers or models.

```php
class YourLibrary extends ContainerAware
{
    public function myServiceMethod() {
        $this->load->library('email');

        // Autocomplete and method prediction works out of the box :)
        return $this->email->from('noreply@example.com')
                ->to('recipient@example.com')
                ->subject('Hello')
                ->body('Hello World!')
                ->sent();
    }
}
```

If you can't (or don't want) to extend the ContainerAware class then you can implement your own *magic method* `__get()'
and to reuse a static helper provided by [ContainerAware](../../../tree/master/pepiscms/application/classes/ContainerAware.php):

```php
class YourAlternativeLibrary
{
    public function __get($var)
    {
        return ContainerAware::__doGet($var);
    }

    public function myServiceMethod() {
        $this->load->library('email');

        // Autocomplete and method prediction does not work out of the box :(
        return $this->email->from('noreply@example.com')
                ->to('recipient@example.com')
                ->subject('Hello')
                ->body('Hello World!')
                ->sent();
    }
}
```

## The old school way (CodeIgniter default)

The below code is for demonstration only:

```php
class YourOldScoolLibrary
{
    public function myServiceMethod() {
        CI_Controller::get_instance()->load->library('email');

        // Autocomplete and method prediction does not work out of the box :(
        return CI_Controller::get_instance()->email->from('noreply@example.com')
                ->to('recipient@example.com')
                ->subject('Hello')
                ->body('Hello World!')
                ->sent();
    }
}
```
