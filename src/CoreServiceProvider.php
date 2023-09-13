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
        if (env('IS_USER_REGISTERED') == 1) {
            $this->app->singleton('pluginManager', function () {
                return \Core\Models\Plugin::all();
            });

            $this->app->singleton('ThemeManager', function () {
                return \Core\Models\Themes::all();
            });
        }
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
        if (env('IS_USER_REGISTERED') == 1) {
            $this->registerPlugins();
            $this->registerTheme();
        }
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

    public function registerPlugins()
    {
        $plugins = getActivePlugins();

        foreach ($plugins as $plugin) {
            //Merge config
            $has_config = file_exists(base_path('plugins/' . $plugin->location . '/config/config.php'));
            if ($has_config) {
                $this->mergeConfigFrom(base_path('plugins/' . $plugin->location . '/config/config.php'), $plugin->location);
            }
            //Load helper
            $has_helpers = file_exists(base_path('plugins/' . $plugin->location . '/helpers/helpers.php'));
            if ($has_helpers) {
                require_once(base_path('plugins/' . $plugin->location . '/helpers/helpers.php'));
            }
            //Load view
            $this->loadViewsFrom(base_path('plugins/' . $plugin->location . '/views'), 'plugin/' . $plugin->location);

            //Generate Namespace
            $loader = new ClassLoader;
            $loader->setPsr4($plugin->namespace, base_path('plugins/' . $plugin->location . '/src'));
            $loader->register(true);
        }
    }

    public function registerTheme()
    {
        $active_theme = getActiveTheme();
        if ($active_theme != null) {
            //Merge config
            $has_config = file_exists(base_path('themes/' . $active_theme->location .  '/config/config.php'));
            if ($has_config) {
                $this->mergeConfigFrom(base_path('themes/' . $active_theme->location .  '/config/config.php'), $active_theme->location);
            }

            //Load helper functions
            $has_helpers = file_exists(base_path('themes/' . $active_theme->location . '/helpers/helpers.php'));
            if ($has_helpers) {
                require_once(base_path('themes/' . $active_theme->location . '/helpers/helpers.php'));
            }

            //Generate namespace
            $loader = new ClassLoader;
            $loader->setPsr4($active_theme->namespace, base_path('themes/' . $active_theme->location . '/src'));
            $loader->register(true);
            //Load view
            $this->loadViewsFrom(base_path('themes/' . $active_theme->location . '/resources/views'), 'theme/' . $active_theme->location);
        }
    }
}
