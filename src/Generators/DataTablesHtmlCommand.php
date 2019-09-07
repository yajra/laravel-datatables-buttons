<?php

namespace Yajra\DataTables\Generators;

use Illuminate\Support\Str;

class DataTablesHtmlCommand extends DataTablesMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datatables:html
                            {name : The name of the DataTable html.}
                            {--dom= : The dom of the DataTable.}
                            {--buttons= : The buttons of the DataTable.}
                            {--table= : Scaffold columns from the table.}
                            {--builder : Ignore, added to work with parent generator.}
                            {--columns= : The columns of the DataTable.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DataTable html class.';

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
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        $this->replaceBuilder($stub)
             ->replaceColumns($stub)
             ->replaceButtons($stub)
             ->replaceDOM($stub)
             ->replaceTableId($stub);

        return $stub;
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
}
