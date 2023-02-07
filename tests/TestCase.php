<?php

namespace Yajra\DataTables\Buttons\Tests;

use Barryvdh\Snappy\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Yajra\DataTables\Buttons\Tests\Models\Role;
use Yajra\DataTables\Buttons\Tests\Models\User;
use Yajra\DataTables\Buttons\Tests\Providers\TestServiceProvider;
use Yajra\DataTables\ButtonsServiceProvider;
use Yajra\DataTables\DataTablesServiceProvider;
use Yajra\DataTables\HtmlServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateDatabase();
        $this->seedDatabase();
    }

    protected function migrateDatabase(): void
    {
        /** @var \Illuminate\Database\Schema\Builder $schemaBuilder */
        $schemaBuilder = $this->app['db']->connection()->getSchemaBuilder();
        if (! $schemaBuilder->hasTable('users')) {
            $schemaBuilder->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email');
                $table->string('user_type')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->timestamps();
            });
        }
        if (! $schemaBuilder->hasTable('posts')) {
            $schemaBuilder->create('posts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->unsignedInteger('user_id');
                $table->timestamps();
                $table->softDeletes();
            });
        }
        if (! $schemaBuilder->hasTable('roles')) {
            $schemaBuilder->create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('role');
                $table->timestamps();
            });
        }
        if (! $schemaBuilder->hasTable('role_user')) {
            $schemaBuilder->create('role_user', function (Blueprint $table) {
                $table->unsignedInteger('role_id');
                $table->unsignedInteger('user_id');
                $table->timestamps();
            });
        }
    }

    protected function seedDatabase(): void
    {
        $adminRole = Role::create(['role' => 'Administrator']);
        $userRole = Role::create(['role' => 'User']);

        collect(range(1, 20))->each(function ($i) use ($userRole) {
            /** @var User $user */
            $user = User::query()->create([
                'name' => 'Record-'.$i,
                'email' => 'Email-'.$i.'@example.com',
            ]);

            collect(range(1, 3))->each(function ($i) use ($user) {
                $user->posts()->create([
                    'title' => "User-{$user->id} Post-{$i}",
                ]);
            });

            if ($i % 2) {
                $user->roles()->attach(Role::all());
            } else {
                $user->roles()->attach($userRole);
            }
        });
    }

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
            TestServiceProvider::class,
            DataTablesServiceProvider::class,
            ButtonsServiceProvider::class,
            HtmlServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    public function getAjax(string $uri, array $headers = []): TestResponse
    {
        return $this->getJson($uri, array_merge(['X-Requested-With' => 'XMLHttpRequest'], $headers));
    }

    public function postAjax(string $uri, array $headers = []): TestResponse
    {
        return $this->postJson($uri, array_merge(['X-Requested-With' => 'XMLHttpRequest'], $headers));
    }
}
