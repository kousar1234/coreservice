<?php

namespace ThemeLooks\CoreService;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\Facades\View;
use Core\Views\Composer\Core;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadCoreNamespace();
        $this->loadCoreHelpers();
        $this->loadViewsFrom(base_path('Core/resources/views'), 'core');
        View::composer(['core::base.layouts.header', 'core::base.layouts.master', 'core::base'], Core::class);
    }

    public function loadCoreNamespace()
    {
        $loader = new ClassLoader;
        $loader->setPsr4('Core\\', base_path('Core'));
        $loader->register(true);
    }

    public function loadCoreHelpers()
    {
        $helperFiles = glob(base_path('Core/Helpers/*.php'));
        foreach ($helperFiles as $helperFile) {
            if (file_exists($helperFile)) {
                require_once($helperFile);
            }
        }
    }
}
