<?php

namespace App\Providers;

use App\Salon\Employee;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Tests\Support\Listeners\MarkRouteAsTouched;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     * In addition, it is set as the URL generator's root namespace.
     *
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->bind();

        if ($this->app->environment('testing', 'integration')) {
            $this->registerListenersForTesting();
        }
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace.'\API')
             ->group(base_path('routes/api.php'));
    }

    /**
     * Define the model bindings for the application
     *
     */
    protected function bind() : void
    {
        $this->bindEmployeeModel();
    }

    /**
     * Define the "professional" model route bind for the application.
     *
     * @return void
     */
    protected function bindEmployeeModel() : void
    {
        Route::bind('professional', function ($professional, $route) {
            // If the parameter is typed hint as an Employee Eloquent Model, we fetch the
            // professional and return the Model as the parameters.
            if ($this->parameterIsModelTypeHinted('professional', $route)) {
                return Employee::professional()->findOrFail($professional);
            }

            // If not, problably is the professional id or some other attribute value of the
            // employee, so we just return it.
            return $professional;
        });
    }

    /**
     * Returns if the given parameter is typed hinted as an Eloquent model in the
     * route / controller signature.
     *
     * @param string $name The parameter name to be checked
     * @param Illuminate\Routing\Route $route
     * @return bool
     */
    protected function parameterIsModelTypeHinted(string $parameter, $route) : bool
    {
        // get the parameter typed hynted as Eloquent Models
        $modelParameters = $route->signatureParameters(Model::Class);

        return ! empty(Arr::where($modelParameters, function ($routeParameter) use ($parameter) {
            return $routeParameter->name == $parameter;
        }));
    }

    /**
     * When on test like environments, register some helpfull
     * route matcher listeners to help test classes
     */
    protected function registerListenersForTesting() : void
    {
        // Register a route matcher listener to make assertions based on the
        // uri and the truth-test callback
        Route::matched(MarkRouteAsTouched::class);
    }

}
