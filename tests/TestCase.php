<?php

namespace Yajra\DataTables\Buttons\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Set up the environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Yajra\DataTables\DataTablesServiceProvider::class,
            \Yajra\DataTables\ButtonsServiceProvider::class,
            \Yajra\DataTables\HtmlServiceProvider::class,
        ];
    }
}
