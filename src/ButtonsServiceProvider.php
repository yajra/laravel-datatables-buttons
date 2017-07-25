<?php

namespace Yajra\DataTables;

use Illuminate\Support\ServiceProvider;
use Yajra\DataTables\Generators\DataTablesMakeCommand;
use Yajra\DataTables\Generators\DataTablesScopeCommand;

class ButtonsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'datatables');

        $this->publishAssets();

        $this->registerCommands();
    }

    /**
     * Publish datatables assets.
     */
    protected function publishAssets()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'datatables-buttons');
        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('datatables-buttons.php'),
        ], 'datatables-buttons');

        $this->publishes([
            __DIR__ . '/resources/assets/buttons.server-side.js' => public_path('vendor/datatables/buttons.server-side.js'),
        ], 'datatables-buttons');

        $this->publishes([
            __DIR__ . '/resources/views' => base_path('/resources/views/vendor/datatables'),
        ], 'datatables-buttons');
    }

    /**
     * Register datatables commands.
     */
    protected function registerCommands()
    {
        $this->commands(DataTablesMakeCommand::class);
        $this->commands(DataTablesScopeCommand::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(HtmlServiceProvider::class);
        if (class_exists('\Maatwebsite\Excel\ExcelServiceProvider')) {
            $this->app->register('\Maatwebsite\Excel\ExcelServiceProvider');
        }
    }
}
