<?php

namespace ThemeLooks\CoreService;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;

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
            $this->loadCoreNamespace();
            $this->loadCoreCommands();
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

        $this->pushCoreServiceMiddleware();
        $this->pushCoreMiddleware();
    }

    public function pushCoreMiddleware()
    {
        $config_path = base_path('Core/Http/Middleware/config.json');
        $this->pushMiddlewareClass($config_path);
    }

    public function pushMiddlewareClass($config_path)
    {
        if (file_exists($config_path)) {
            $kernel = $this->app->make(Kernel::class);

            $config_file = @file_get_contents($config_path, true);
            if ($config_file != false) {
                $config_content = json_decode($config_file, true);
                if (isset($config_content['middleWares'])) {
                    foreach ($config_content['middleWares'] as $middleware) {
                        if (isset($middleware['alias']) && isset($middleware['path']) && class_exists($middleware['path'])) {
                            app('router')->aliasMiddleware($middleware['alias'], $middleware['path']);
                        }

                        if (isset($middleware['group']) && isset($middleware['path']) && class_exists($middleware['path'])) {
                            app('router')->pushMiddlewareToGroup($middleware['group'], $middleware['path']);
                        }

                        if (!isset($middleware['alias']) && !isset($middleware['group']) && isset($middleware['path']) && class_exists($middleware['path'])) {
                            $kernel->pushMiddleware($middleware['path']);
                        }
                    }
                }
            }
        }
    }

    public function pushCoreServiceMiddleware()
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(\ThemeLooks\CoreService\Http\Middleware\SystemInstalled::class);
        app('router')->aliasMiddleware('redirectIfInstalled', \ThemeLooks\CoreService\Http\Middleware\RedirectIfInstalled::class);
        app('router')->aliasMiddleware('install', \ThemeLooks\CoreService\Http\Middleware\SystemInstalled::class);
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

    public function loadCoreCommands()
    {
        $commandsPath = base_path('Core/Console/Commands');
        foreach (glob($commandsPath . '/*.php') as $file) {
            $commandClass = 'Core\\Console\\Commands\\' . pathinfo($file, PATHINFO_FILENAME);

            if (class_exists($commandClass)) {
                $this->commands($commandClass);
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

            //push middleware
            $config_path = base_path('plugins/' . $plugin->location . '/plugin.json');
            $this->pushMiddlewareClass($config_path);
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

            //push middleware
            $config_path = base_path('themes/' . $active_theme->location . '/theme.json');
            $this->pushMiddlewareClass($config_path);
        }
    }
}
