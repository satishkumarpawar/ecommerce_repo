<?php

namespace ACME\UserSurvey\Providers;

use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Illuminate\Support\ServiceProvider;
use Webkul\Category\Models\CategoryProxy;
use Webkul\Category\Observers\CategoryObserver;

class CategoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        CategoryProxy::observe(CategoryObserver::class);

        $this->registerEloquentFactoriesFrom(__DIR__ . '/../Database/Factories');
    }

    public function getId()
    {
    }
    public function getNamespaceRoot()
    {
    }
    
    public function register()
    {
        //
        $this->app->bind(\ACME\UserSurvey\Contracts\Category::class, \ACME\UserSurvey\Repositories\CategoryRepository::class);
    }


    /**
     * Register factories.
     *
     * @param  string  $path
     * @return void
     */
    protected function registerEloquentFactoriesFrom($path): void
    {
      $this->app->make(EloquentFactory::class)->load($path);
   
    }
}