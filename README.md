
# Usage

```php
use Tsterker\Flextable\Manager;

$manager = new Manager($optionalConnection = 'custom');

$connection = $manager->getConnection();

$manager->migrate('/absolute/path/to/migrations');

$manager->seed(\Acme\DatabaseSeeder::class, $alternativeFactories = '/absolute/path/to/factories');
```

In case your Models have an explicit `$connection` property set:

A). Specify the connection name during `new Manager('custom-connection)`(:warning: it will be replaced in the configuration!)

B). Use the `IsFlextable` trait on a base model that all relevant models inherit from.

```php
use Tsterker\Flextable\IsFlextable;
use Tsterker\Flextable\Manager;

class BaseModel extends \Illuminate\Eloquent\Model
{
    use IsFlextable;

    protected $connection = 'custom-connection';
}
```
