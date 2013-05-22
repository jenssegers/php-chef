Chef
====

The Chef Server API is used to provide access to objects on the Chef Server, including nodes, environments, roles, cookbooks (and cookbook versions), and to manage an API client list and the associated RSA public key-pairs.

*This library is a generic library and has additional support for the Laravel framework.*

Installation
------------

Add `jenssegers/chef` as a requirement to composer.json:

```yaml
{
    "require": {
        "jenssegers/chef": "dev-master"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

Usage
-----

Create a chef object like this:

```php
// composer
require_once 'vendor/autoload.php';
use Jenssegers\Chef\Chef;

// create chef object
$chef = new Chef($server, $client, $key, $version);

// API request
$response = $chef->api($endpoint, $method, $data);
```

See http://docs.opscode.com/api_chef_server.html for all available endpoints.

Laravel
-------
 
Register the Chef package with Laravel in `app/config/app.php`, add the following provider:

```php
'Jenssegers\Chef\ChefServiceProvider',
```

And this alias:

```php
'Chef'            => 'Jenssegers\Chef\Facades\Chef'
```

Create a copy of the configuration file using Artisan:

```bash
php artisan config:publish jenssegers/chef
```

Edit the created configuration file in `app/config/packages/jenssegers/chef/config.php` to match your environment:

    'server'  = the URL for the Chef Server
    'client'  = the name used when authenticating to a Chef Server
    'key'     = the location of the file which contains the client key
    'version' = the version of the Chef Server API that is being used

Examples
--------

Get nodes:

```php
$nodes = $chef->get('/nodes');
```

Create a data bag:

```php
$bag = new stdClass;
$bag->name = "test";

$resp = $chef->post('/data', $bag);
```

Update a node:

```php
$node = $chef->get('/nodes/webserver1');
$node->attributes->type = "webserver";

$chef->put('/nodes/webserver1', $node);
```

Delete a data bag:

```php
$chef->delete('/data/test/item');
```
