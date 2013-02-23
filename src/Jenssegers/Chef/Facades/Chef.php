<?php namespace Jenssegers\Chef\Facades;

use Illuminate\Support\Facades\Facade;

class Chef extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'chef'; }

}