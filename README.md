Chef
====

The Chef Server API is used to provide access to objects on the Chef Server, including nodes, environments, roles, cookbooks (and cookbook versions), and to manage an API client list and the associated RSA public key-pairs.

Installation
============

Add `jenssegers/chef` as a requirement to composer.json:

    {
        "require": {
            "jenssegers/chef": "dev-master"
        }
    }

Update your packages with `composer update` or install with `composer install`.

Register the Chef package with Laravel in `app/config/app.php`, add the following provider:

    'Jenssegers\Chef\ChefServiceProvider',

And this alias:

    'Chef'            => 'Jenssegers\Chef\Facades\Chef'

Configuration
=============

Create a copy of the configuration file using Artisan:

    $ php artisan config:publish jenssegers/chef

Edit the created configuration file in `app/config/packages/jenssegers/chef/config.php` to match your environment:

    'server'  = the URL for the Chef Server
    'client'  = the name used when authenticating to a Chef Server
    'key'     = the location of the file which contains the client key
    'version' = the version of the Chef Server API that is being used

Usage
=====

All API calls are made using the `Chef::api($endpint, $method, $data)` method. This method has 3 parameters:

 * $endpoint: the API endpoint you want to call, check the Chef documentation for all available endpoints
 * $method: the request method, default is GET
 * $data: the data you want to send, this will be converted to json automatically

More about the available endpoints: http://docs.opscode.com/api_chef_server.html

Example
=======

Get nodes:

    $nodes = Chef::api('nodes');

Create a databag:

    // create databag
    $bag = new stdClass;
    $bag->name = "test";

    $resp = Chef::api('data', 'POST', $bag);