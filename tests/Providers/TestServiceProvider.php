<?php

namespace Yajra\DataTables\Buttons\Tests\Providers;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'tests');
    }
}
