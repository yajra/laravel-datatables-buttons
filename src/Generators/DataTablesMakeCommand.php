<?php

namespace Yajra\Datatables\Generators;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class DataTablesMakeCommand.
 *
 * @package Yajra\Datatables\Generators
 * @author  Arjay Angeles <aqangeles@gmail.com>
 */
class DataTablesMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datatables:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DataTable service class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'DataTable';

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        return $this->replaceModelImport($stub)
                    ->replaceModel($stub)
                    ->replaceFilename($stub);
    }

    /**
     * Replace model name.
     *
     * @param string $stub
     * @return mixed
     */
    protected function replaceModel(&$stub)
    {
        $model = explode('\\', $this->getModel());
        $model = array_pop($model);
        $stub  = str_replace('ModelName', $model, $stub);

        return $this;
    }

    /**
     * Get model name to use.
     */
    protected function getModel()
    {
        $name           = $this->getNameInput();
        $rootNamespace  = $this->laravel->getNamespace();
        $modelNamespace = $this->laravel['config']->get('datatables-buttons.namespace.model');

        return $this->option('model')
            ? $rootNamespace . "\\" . ($modelNamespace ? $modelNamespace . "\\" : "") . str_singular($name)
            : $rootNamespace . "\\User";
    }

    /**
     * Replace model import.
     *
     * @param string $stub
     * @return $this
     */
    protected function replaceModelImport(&$stub)
    {
        $stub = str_replace(
            'DummyModel', str_replace('\\\\', '\\', $this->getModel()), $stub
        );

        return $this;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/datatables.stub';
    }

    /**
     * Replace the filename.
     *
     * @param string $stub
     * @return string
     */
    protected function replaceFilename(&$stub)
    {
        $stub = str_replace(
            'DummyFilename', str_slug($this->getNameInput()), $stub
        );

        return $stub;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_NONE, 'Use the provided name as the model.', null],
        ];
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param  string $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $rootNamespace = $this->laravel->getNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        if (! Str::contains(Str::lower($name), 'datatable')) {
            $name .= 'DataTable';
        }

        return $this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\" . $this->laravel['config']->get('datatables-buttons.namespace.base', 'DataTables');
    }
}
