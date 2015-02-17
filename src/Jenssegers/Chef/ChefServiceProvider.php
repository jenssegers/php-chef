<?php namespace Jenssegers\Chef;

use Illuminate\Support\ServiceProvider;

class ChefServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
         __DIR__ . '/../../config/config.php' => config_path('chef.php')
         ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['chef'] = $this->app->share(function () {
            // load settings from configuration
            $server     = config('chef.server');
            $client     = config('chef.client');
            $key        = config('chef.key');
            $version    = config('chef.version');
            $enterprise = config('chef.enterprise');

            return new Chef($server, $client, $key, $version, $enterprise);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('Chef');
    }
}
