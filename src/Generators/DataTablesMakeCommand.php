<?php

namespace Yajra\DataTables\Generators;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DataTablesMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datatables:make
                            {name : The name of the DataTable.}
                            {--model= : The name of the model to be used.}
                            {--model-namespace= : The namespace of the model to be used.}
                            {--action= : The path of the action view.}
                            {--table= : Scaffold columns from the table.}
                            {--builder : Extract html() to a Builder class.}
                            {--dom= : The dom of the DataTable.}
                            {--buttons= : The buttons of the DataTable.}
                            {--columns= : The columns of the DataTable.}';

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

    public function handle()
    {
        parent::handle();

        if ($this->option('builder')) {
            $this->call('datatables:html', [
                'name'      => $this->getNameInput(),
                '--columns' => $this->option('columns') ?: $this->laravel['config']->get(
                    'datatables-buttons.generator.columns',
                    'id,add your columns,created_at,updated_at'
                ),
                '--buttons' => $this->option('buttons') ?: $this->laravel['config']->get(
                    'datatables-buttons.generator.buttons',
                    'create,export,print,reset,reload'
                ),
                '--dom'     => $this->option('dom') ?: $this->laravel['config']->get(
                    'datatables-buttons.generator.dom',
                    'Bfrtip'
                ),
                '--table'   => $this->option('table'),
            ]);
        }
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $this->replaceModelImport($stub)
             ->replaceModel($stub)
             ->replaceBuilder($stub)
             ->replaceColumns($stub)
             ->replaceButtons($stub)
             ->replaceDOM($stub)
             ->replaceTableId($stub)
             ->replaceAction($stub)
             ->replaceFilename($stub);

        return $stub;
    }

    /**
     * Replace the filename.
     *
     * @param string $stub
     * @return $this
     */
    protected function replaceFilename(&$stub)
    {
        $stub = str_replace(
            'DummyFilename', preg_replace('#datatable$#i', '', $this->getNameInput()), $stub
        );

        return $this;
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
        return $this->option('action') ?: Str::lower($this->getNameInput()) . '.action';
    }

    /**
     * Replace columns.
     *
     * @param string $stub
     * @return $this
     */
    protected function replaceTableId(&$stub)
    {
        $stub = str_replace(
            'DummyTableId', Str::lower($this->getNameInput()) . '-table', $stub
        );

        return $this;
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
        if ($this->option('table')) {
            return $this->parseColumns(Schema::getColumnListing($this->option('table')));
        }

        if ($this->option('columns') != '') {
            return $this->parseColumns($this->option('columns'));
        }

        return $this->parseColumns(
            $this->laravel['config']->get(
                'datatables-buttons.generator.columns',
                'id,add your columns,created_at,updated_at'
            )
        );
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
        $columns = is_array($definition) ? $definition : explode(',', $definition);
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
     * @return \Yajra\DataTables\Generators\DataTablesMakeCommand
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
            $name .= 'DataTable';
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
        $model          = $this->option('model') == '' || $this->option('model-namespace');
        $modelNamespace = $this->option('model-namespace') ? $this->option('model-namespace') : $this->laravel['config']->get('datatables-buttons.namespace.model');

        if ($this->option('model') != '') {
            return $this->option('model');
        }

        return $model
            ? $rootNamespace . '\\' . ($modelNamespace ? $modelNamespace . '\\' : '') . Str::singular($name)
            : $rootNamespace . '\\User';
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
        $stub   = 'datatables.stub';

        if ($this->option('builder')) {
            $stub = 'builder.stub';
        }

        return $config->get('datatables-buttons.stub')
            ? base_path() . $config->get('datatables-buttons.stub') . "/{$stub}"
            : __DIR__ . "/stubs/{$stub}";
    }
}
