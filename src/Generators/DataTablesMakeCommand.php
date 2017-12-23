<?php

namespace Yajra\DataTables\Generators;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class DataTablesMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datatables:make
                            {name : The name of the datatable.}
                            {--model : The name of the model to be used.}
                            {--model-namespace= : The namespace of the model to be used.}
                            {--action= : The path of the action view.}
                            {--columns= : The columns of the datatable.}';

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
                    ->replaceColumns($stub)
                    ->replaceAction($stub)
                    ->replaceFilename($stub);
    }

    /**
     * Replace model name.
     *
     * @param string $stub
     * @return \Yajra\DataTables\Generators\DataTablesMakeCommand
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
        $model          = $this->option('model') || $this->option('model-namespace');
        $modelNamespace = $this->option('model-namespace') ? $this->option('model-namespace') : $this->laravel['config']->get('datatables-buttons.namespace.model');

        return $model
            ? $rootNamespace . '\\' . ($modelNamespace ? $modelNamespace . '\\' : '') .  str_singular($name)
            : $rootNamespace . '\\User';
    }

    /**
     * Replace columns.
     *
     * @param string $stub
     * @return $this
     */
    protected function replaceColumns(&$stub)
    {
        $stub = str_replace(
            'DummyColumns', $this->getColumns(), $stub
        );

        return $this;
    }

    /**
     * Get the columns to be used.
     *
     * @return string
     */
    protected function getColumns()
    {
        if ($this->option('columns') != '') {
            return $this->parseArray($this->option('columns'));
        } else {
            return $this->parseArray('id,add your columns,created_at,updated_at');
        }
    }

    /**
     * Parse array from definition.
     *
     * @param  string  $definition
     * @param  string  $delimiter
     * @param  int     $indentation
     * @return string
     */
    protected function parseArray($definition, $delimiter = ',', $indentation = 12)
    {
        return str_replace($delimiter, "',\n" . str_repeat(' ', $indentation) . "'", $definition);
    }

    /**
     * Replace the action.
     *
     * @param string $stub
     * @return \Yajra\DataTables\Generators\DataTablesMakeCommand
     */
    protected function replaceAction(&$stub)
    {
        $stub = str_replace(
            'DummyAction', $this->getAction(), $stub
        );

        return $this;
    }

    /**
     * Set the action view to be used.
     *
     * @return string
     */
    protected function getAction()
    {
        return $this->option('action') ? $this->option('action') : Str::lower($this->getNameInput()) . '.action';
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
        $config = $this->laravel['config'];

        return $config->get('datatables-buttons.stub')
            ? base_path() . $config->get('datatables-buttons.stub') . '/datatables.stub'
            : __DIR__ . '/stubs/datatables.stub';
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
            'DummyFilename', preg_replace('#datatable$#i', '', $this->getNameInput()), $stub
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
            ['action', null, InputOption::VALUE_OPTIONAL, 'Path to action column template.', null],
            ['columns', null, InputOption::VALUE_OPTIONAL, 'Use the provided columns.', null],
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
        return $rootNamespace . '\\' . $this->laravel['config']->get('datatables-buttons.namespace.base', 'DataTables');
    }
}
