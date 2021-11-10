<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    private ?ViewFactory $viewFactory;

    private Router $router;

    public function __construct(
        Application $app
    )
    {
        try {
            $this->viewFactory = $app['view'];
        } catch (Throwable $exception) {
            // DO not share view
        }

        $this->router = $app['router'];

        parent::__construct($app);
    }

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
        if (isset($this->viewFactory)) {
            $this->viewFactory->composer(
                '*',
                function (View $view) {
                    $view->with('user', Auth::user());
                }
            );

            $this->viewFactory->share('router', $this->router);
        }
    }
}
