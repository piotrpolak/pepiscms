# Benchmarking

PepisCMS was designed with small memory footprint and processing power in mind.

To enable profiler please change the following property:

```php

// application/config.php

$config['enable_profiler'] = TRUE;
```

You might observe that for concurrent requests access check and menu rendering is skipped.