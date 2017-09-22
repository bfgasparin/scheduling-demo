<?php

namespace App\Providers;

use App\User;
use InvalidArgumentException;
use NewAgeIpsum\NewAgeProvider;
use Faker\Generator as FakerGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * The Providers to add to Faker Generator
     *
     * @var array
     */
    static protected $fakerProviders = ['Date', 'NewPhoneNumber', 'Salon\Service'];

    /**
     * Bootstrap the application database services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('local', 'testing', 'integration')) {
            $this->bootstrapFaker();
            $this->decorateEloquentFactoryBuilder();
        }

        $this->registerModelMorphMapRelations();
    }

    /**
     * Configure The Faker Generator
     *
     * The first thing to to before defining the factories for application models
     * is to configure Faker Genarator.
     *
     * Faker Generator delegates all responsabilities to Faker\Provider objects.
     *
     * Here we add custom providers to help Model Factories to define create
     * models
     *
     * Note that Faker starts looking the Providers from the last one, so to override
     * existing Providers, just add the new Provider alter the provider you want to
     * override.
     *
     * @see Faker\Provider
     * @see https://github.com/fzaninotto/Faker#faker-internals-understanding-providers
     */
    protected function bootstrapFaker() : void
    {
        tap($this->app->make(FakerGenerator::class), function ($faker) {
            $this->addAppProviders($faker);
            $faker->addProvider(new NewAgeProvider($faker));
        });
    }

    /**
     * Add Application Specific Providers for Faker Generator
     *
     * @param FakerGenerator $faker
     */
    protected function addAppProviders(FakerGenerator $faker) : void
    {
        $locale = $this->app['config']->get('app.faker_locale', 'en_US');
        foreach (static::$fakerProviders as $provider) {
            $providerClassName = $this->getProviderClassname($provider, $locale);

            $faker->addProvider(new $providerClassName($faker));
        }
    }

    /**
     * Chain the Provider directory trying to find and return the full qualified
     * classe name of the Provider
     *
     * @param string $provider
     * @param string $locale
     * @return string
     */
    protected function getProviderClassname(string $provider, string $locale) : string
    {
        if ($providerClass = $this->findProviderClassname($provider, $locale)) {
            return $providerClass;
        }

        // fallback to no locale
        if ($providerClass = $this->findProviderClassname($provider)) {
            return $providerClass;
        }

        throw new InvalidArgumentException("Unable to find provider $provider with locale '$locale'");
    }

    /**
     * Return the full qualified classe name of the Provider
     *
     * @param string $provider
     * @param string $locale
     * @return string
     */
    protected function findProviderClassname(string $provider, string $locale = '') : ?string
    {
        $providerClass = 'Faker\\Provider\\' . ($locale
            ? sprintf('%s\%s', $locale, $provider)
            : sprintf('%s', $provider));

        if (class_exists($providerClass, true)) {
            return $providerClass;
        }

        return null;
    }

    /**
     * Register the morph map of the Eloquent relations
     *
     * @return void
     */
    protected function registerModelMorphMapRelations() : void
    {
        // nothing to register
    }

    /**
     * Decoreate Eloquent Factory Builder with some helpful methods
     *
     * @return void
     */
    protected function decorateEloquentFactoryBuilder() : void
    {
        FactoryBuilder::macro('firstOrCreate', function (array $attributes, array $values = []) {
            return $this->class::where($attributes)->first()
                ?? factory($this->class)->create($attributes + $values);
        });
    }
}
