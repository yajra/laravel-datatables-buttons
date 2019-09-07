<?php

namespace Yajra\DataTables\Generators;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class DataTablesHtmlCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datatables:html
                            {name : The name of the datatable html.}
                            {--dom= : The dom of the datatable.}
                            {--buttons= : The buttons of the datatable.}
                            {--columns= : The columns of the datatable.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new dataTable html class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'DataTableHtml';

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        return $this->replaceBuilder($stub)
                    ->replaceColumns($stub)
                    ->replaceButtons($stub)
                    ->replaceDOM($stub)
                    ->replaceTableId($stub);
    }

    /**
     * Replace columns.
     *
     * @param string $stub
     * @return string
     */
    protected function replaceTableId(&$stub)
    {
        $stub = str_replace(
            'DummyTableId', Str::lower($this->getNameInput()) . '-table', $stub
        );

        return $stub;
    }

    /**
     * Replace dom.
     *
     * @param string $stub
     * @return $this
     */
    protected function replaceDOM(&$stub)
    {
        $stub = str_replace(
            'DummyDOM',
            $this->option('dom') ?: $this->laravel['config']->get('datatables-buttons.generator.dom', 'Bfrtip'),
            $stub
        );

        return $this;
    }

    /**
     * Replace buttons.
     *
     * @param string $stub
     * @return $this
     */
    protected function replaceButtons(&$stub)
    {
        $stub = str_replace(
            'DummyButtons', $this->getButtons(), $stub
        );

        return $this;
    }

    /**
     * Get the columns to be used.
     *
     * @return string
     */
    protected function getButtons()
    {
        if ($this->option('buttons') != '') {
            return $this->parseButtons($this->option('buttons'));
        } else {
            return $this->parseButtons(
                $this->laravel['config']->get(
                    'datatables-buttons.generator.buttons',
                    'create,export,print,reset,reload'
                )
            );
        }
    }

    /**
     * Parse array from definition.
     *
     * @param string $definition
     * @param int $indentation
     * @return string
     */
    protected function parseButtons($definition, $indentation = 24)
    {
        $columns = explode(',', $definition);
        $stub    = '';
        foreach ($columns as $key => $column) {
            $indent    = '';
            $separator = ',';

            if ($key < count($columns) - 1) {
                $indent = PHP_EOL . str_repeat(' ', $indentation);
            }

            if ($key == count($columns) - 1) {
                $separator = '';
            }

            $stub .= "Button::make('{$column}')" . $separator . $indent;
        }

        return $stub;
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
            return $this->parseColumns($this->option('columns'));
        } else {
            return $this->parseColumns(
                $this->laravel['config']->get(
                    'datatables-buttons.generator.columns',
                    'id,add your columns,created_at,updated_at'
                )
            );
        }
    }

    /**
     * Parse array from definition.
     *
     * @param string $definition
     * @param int $indentation
     * @return string
     */
    protected function parseColumns($definition, $indentation = 12)
    {
        $columns = explode(',', $definition);
        $stub    = '';
        foreach ($columns as $key => $column) {
            $stub .= "Column::make('{$column}'),";

            if ($key < count($columns) - 1) {
                $stub .= PHP_EOL . str_repeat(' ', $indentation);
            }
        }

        return $stub;
    }

    /**
     * Replace builder name.
     *
     * @param string $stub
     * @return self
     */
    protected function replaceBuilder(&$stub)
    {
        $name  = $this->qualifyClass($this->getNameInput());
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        $stub = str_replace('DummyBuilder', $class . 'Html', $stub);

        return $this;
    }

    /**
     * Parse the name and format according to the root namespace.
     *
     * @param string $name
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
            $name .= 'DataTableHtml';
        }

        return $this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . $this->laravel['config']->get('datatables-buttons.namespace.base', 'DataTables');
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
            ? base_path() . $config->get('datatables-buttons.stub') . '/html.stub'
            : __DIR__ . '/stubs/html.stub';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['columns', null, InputOption::VALUE_OPTIONAL, 'Use the provided columns.', null],
            ['buttons', null, InputOption::VALUE_OPTIONAL, 'Use the provided buttons.', null],
            ['dom', null, InputOption::VALUE_OPTIONAL, 'Use the provided DOM.', null],
        ];
    }
}
