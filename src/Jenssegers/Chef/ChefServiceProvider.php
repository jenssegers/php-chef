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
        $this->package('jenssegers/chef');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['chef'] = $this->app->share(function($app)
        {
            // load settings from configuration
            $server = $app['config']->get('chef::server');
            $client = $app['config']->get('chef::client');
            $key = $app['config']->get('chef::key');
            $version = $app['config']->get('chef::version');
            $enterprise = $app['config']->get('chef::enterprise');

            return new Chef($server, $client, $key, $version,$enterprise);
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
