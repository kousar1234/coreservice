<?php

namespace ThemeLooks\CoreService;

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
        $this->loadCoreHelpers();
        $this->loadViewsFrom(base_path('Core/resources/views'), 'core');
        View::composer(['core::base.layouts.header', 'core::base.layouts.master', 'core::base'], Core::class);
    }

    public function loadCoreHelpers()
    {
        if (file_exists(base_path('Core/Helpers/Helpers.php'))) {
            require_once(base_path('Core/Helpers/Helpers.php'));
        }

        if (file_exists(base_path('Core/Helpers/Theme.php'))) {
            require_once(base_path('Core/Helpers/Theme.php'));
        }

        if (file_exists(base_path('Core/Helpers/Plugin.php'))) {
            require_once(base_path('Core/Helpers/Plugin.php'));
        }

        if (file_exists(base_path('Core/Helpers/Notification.php'))) {
            require_once(base_path('Core/Helpers/Notification.php'));
        }

        if (file_exists(base_path('Core/Helpers/Media.php'))) {
            require_once(base_path('Core/Helpers/Media.php'));
        }
    }
}
