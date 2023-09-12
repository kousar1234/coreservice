<?php

namespace ThemeLooks\CoreService;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class CoreRouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            $this->RegisterCoreRoute();
            if (env('IS_USER_REGISTERED') == 1) {
                $this->registerPluginRoute();
                $this->registerThemeRoute();
            }
        });
    }

    public function RegisterCoreRoute()
    {
        Route::middleware('web')
            ->prefix(getAdminPrefix())
            ->group(base_path('Core/routes/core.php'));
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('Core/routes/api.php'));
    }


    public function registerPluginRoute()
    {
        $plugins = getActivePlugins();
        foreach ($plugins as $plugin) {
            if (file_exists(base_path('plugins/' . $plugin->location . '/routes/api.php'))) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('plugins/' . $plugin->location . '/routes/api.php'));
            }

            if (file_exists(base_path('plugins/' . $plugin->location . '/routes/web.php'))) {
                Route::middleware('web')
                    ->group(base_path('plugins/' . $plugin->location . '/routes/web.php'));
            }
        }
    }

    public function registerThemeRoute()
    {
        $active_theme = getActiveTheme();
        if ($active_theme != null) {
            if (file_exists(base_path('themes/' . $active_theme->location . '/routes/api.php'))) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('themes/' . $active_theme->location . '/routes/api.php'));
            }

            if (file_exists(base_path('themes/' . $active_theme->location . '/routes/web.php'))) {
                Route::middleware('web')
                    ->group(base_path('themes/' . $active_theme->location . '/routes/web.php'));
            }
        }
    }
}
