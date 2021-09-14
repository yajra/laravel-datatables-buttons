<?php

namespace Yajra\DataTables\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;
use Yajra\DataTables\Contracts\DataTableButtons;
use Yajra\DataTables\Contracts\DataTableScope;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\QueryDataTable;
use Yajra\DataTables\Transformers\DataArrayTransformer;

abstract class DataTable implements DataTableButtons
{
    /**
     * DataTables print preview view.
     *
     * @var string
     */
    protected $printPreview = 'datatables::print';

    /**
     * Name of the dataTable variable.
     *
     * @var string
     */
    protected $dataTableVariable = 'dataTable';

    /**
     * List of columns to be excluded from export.
     *
     * @var string|array
     */
    protected $excludeFromExport = [];

    /**
     * List of columns to be excluded from printing.
     *
     * @var string|array
     */
    protected $excludeFromPrint = [];

    /**
     * List of columns to be exported.
     *
     * @var string|array
     */
    protected $exportColumns = '*';

    /**
     * List of columns to be printed.
     *
     * @var string|array
     */
    protected $printColumns = '*';

    /**
     * Query scopes.
     *
     * @var \Yajra\DataTables\Contracts\DataTableScope[]
     */
    protected $scopes = [];

    /**
     * Html builder.
     *
     * @var \Yajra\DataTables\Html\Builder
     */
    protected $htmlBuilder;

    /**
     * Html builder extension callback.
     *
     * @var callable
     */
    protected $htmlCallback;

    /**
     * Export filename.
     *
     * @var string
     */
    protected $filename = '';

    /**
     * Custom attributes set on the class.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Callback before sending the response.
     *
     * @var callable
     */
    protected $beforeCallback;

    /**
     * Callback after processing the response.
     *
     * @var callable
     */
    protected $responseCallback;

    /**
     * Available button actions. When calling an action, the value will be used
     * as the function name (so it should be available)
     * If you want to add or disable an action, overload and modify this property.
     *
     * @var array
     */
    protected $actions = ['print', 'csv', 'excel', 'pdf'];

    /**
     * @var \Yajra\DataTables\Utilities\Request
     */
    protected $request;

    /**
     * Flag to use fast-excel package for export.
     *
     * @var bool
     */
    protected $fastExcel = false;

    /**
     * Flag to enable/disable fast-excel callback.
     * Note: Disabling this flag can improve you export time.
     * Enabled by default to emulate the same output
     * with laravel-excel.
     *
     * @var bool
     */
    protected $fastExcelCallback = true;

    /**
     * Export class handler.
     *
     * @var string
     */
    protected $exportClass = DataTablesExportHandler::class;

    /**
     * CSV export type writer.
     *
     * @var string
     */
    protected $csvWriter = 'Csv';

    /**
     * Excel export type writer.
     *
     * @var string
     */
    protected $excelWriter = 'Xlsx';

    /**
     * PDF export type writer.
     *
     * @var string
     */
    protected $pdfWriter = 'Dompdf';

    /**
     * Process dataTables needed render output.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return mixed
     */
    public function render($view, $data = [], $mergeData = [])
    {
        if ($this->request()->ajax() && $this->request()->wantsJson()) {
            return app()->call([$this, 'ajax']);
        }

        if ($action = $this->request()->get('action') and in_array($action, $this->actions)) {
            if ($action == 'print') {
                return app()->call([$this, 'printPreview']);
            }

            return app()->call([$this, $action]);
        }

        return view($view, $data, $mergeData)->with($this->dataTableVariable, $this->getHtmlBuilder());
    }

    /**
     * Get DataTables Request instance.
     *
     * @return \Yajra\DataTables\Utilities\Request
     */
    public function request()
    {
        return $this->request ?: $this->request = app('datatables.request');
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        $query = null;
        if (method_exists($this, 'query')) {
            $query = app()->call([$this, 'query']);
            $query = $this->applyScopes($query);
        }

        /** @var \Yajra\DataTables\DataTableAbstract $dataTable */
        $dataTable = app()->call([$this, 'dataTable'], compact('query'));

        if ($callback = $this->beforeCallback) {
            $callback($dataTable);
        }

        if ($callback = $this->responseCallback) {
            $data = new Collection($dataTable->toArray());

            return new JsonResponse($callback($data));
        }

        return $dataTable->toJson();
    }

    /**
     * Display printable view of datatables.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function printPreview()
    {
        $data = $this->getDataForPrint();

        return view($this->printPreview, compact('data'));
    }

    /**
     * Get mapped columns versus final decorated output.
     *
     * @return array
     */
    protected function getDataForPrint()
    {
        $columns = $this->printColumns();

        return $this->mapResponseToColumns($columns, 'printable');
    }

    /**
     * Get printable columns.
     *
     * @return array|string
     */
    protected function printColumns()
    {
        return is_array($this->printColumns) ? $this->toColumnsCollection($this->printColumns) : $this->getPrintColumnsFromBuilder();
    }

    /**
     * Get filtered print columns definition from html builder.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getPrintColumnsFromBuilder()
    {
        return $this->html()->removeColumn(...$this->excludeFromPrint)->getColumns();
    }

    /**
     * Get filtered export columns definition from html builder.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getExportColumnsFromBuilder()
    {
        return $this->html()->removeColumn(...$this->excludeFromExport)->getColumns();
    }

    /**
     * Get columns definition from html builder.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getColumnsFromBuilder()
    {
        return $this->html()->getColumns();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder();
    }

    /**
     * Get DataTables Html Builder instance.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function builder()
    {
        if ($this->htmlBuilder) {
            return $this->htmlBuilder;
        }

        if (method_exists($this, 'htmlBuilder')) {
            return $this->htmlBuilder = app()->call([$this, 'htmlBuilder']);
        }

        return $this->htmlBuilder = app('datatables.html');
    }

    /**
     * Map ajax response to columns definition.
     *
     * @param  mixed  $columns
     * @param  string  $type
     * @return array
     */
    protected function mapResponseToColumns($columns, $type)
    {
        $transformer = new DataArrayTransformer;

        return array_map(function ($row) use ($columns, $type, $transformer) {
            return $transformer->transform($row, $columns, $type);
        }, $this->getAjaxResponseData());
    }

    /**
     * Get decorated data as defined in datatables ajax response.
     *
     * @return array
     */
    protected function getAjaxResponseData()
    {
        $this->request()->merge([
            'start'  => 0,
            'length' => -1,
        ]);

        $response = app()->call([$this, 'ajax']);
        $data     = $response->getData(true);

        return $data['data'];
    }

    /**
     * @return \Yajra\DataTables\Html\Builder
     */
    protected function getHtmlBuilder()
    {
        $builder = $this->html();
        if ($this->htmlCallback) {
            call_user_func($this->htmlCallback, $builder);
        }

        return $builder;
    }

    /**
     * Add html builder callback hook.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function withHtml(callable $callback)
    {
        $this->htmlCallback = $callback;

        return $this;
    }

    /**
     * Add callback before sending the response.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function before(callable $callback)
    {
        $this->beforeCallback = $callback;

        return $this;
    }

    /**
     * Add callback after the response was processed.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function response(callable $callback)
    {
        $this->responseCallback = $callback;

        return $this;
    }

    /**
     * Export results to Excel file.
     *
     * @return void
     */
    public function excel()
    {
        set_time_limit(3600);

        $ext      = '.' . strtolower($this->excelWriter);
        $callback = $this->fastExcel ?
            ($this->fastExcelCallback ? $this->fastExcelCallback() : null)
            : $this->excelWriter;

        return $this->buildExcelFile()->download($this->getFilename() . $ext, $callback);
    }

    /**
     * Build excel file and prepare for export.
     *
     * @return \Maatwebsite\Excel\Concerns\Exportable
     */
    protected function buildExcelFile()
    {
        if ($this->fastExcel) {
            return $this->buildFastExcelFile();
        }

        if (! new $this->exportClass(collect()) instanceof DataTablesExportHandler) {
            $collection = $this->getAjaxResponseData();

            return new $this->exportClass($this->convertToLazyCollection($collection));
        }

        $collection = $this->getDataForExport();

        return new $this->exportClass($this->convertToLazyCollection($collection));
    }

    /**
     * Get export filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename ?: $this->filename();
    }

    /**
     * Set export filename.
     *
     * @param  string  $filename
     * @return DataTable
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return class_basename($this) . '_' . date('YmdHis');
    }

    /**
     * Get mapped columns versus final decorated output.
     *
     * @return array
     */
    protected function getDataForExport()
    {
        $columns = $this->exportColumns();

        return $this->mapResponseToColumns($columns, 'exportable');
    }

    /**
     * Get export columns definition.
     *
     * @return array|string
     */
    protected function exportColumns()
    {
        return is_array($this->exportColumns) ? $this->toColumnsCollection($this->exportColumns) : $this->getExportColumnsFromBuilder();
    }

    /**
     * Convert array to collection of Column class.
     *
     * @param  array  $columns
     * @return Collection
     */
    private function toColumnsCollection(array $columns)
    {
        $collection = collect();
        foreach ($columns as $column) {
            if (isset($column['data'])) {
                $column['title'] = $column['title'] ?? $column['data'];
                $collection->push(new Column($column));
            } else {
                $data          = [];
                $data['data']  = $column;
                $data['title'] = $column;
                $collection->push(new Column($data));
            }
        }

        return $collection;
    }

    /**
     * Export results to CSV file.
     *
     * @return mixed
     */
    public function csv()
    {
        set_time_limit(3600);
        $ext      = '.' . strtolower($this->csvWriter);
        $callback = $this->fastExcel ?
            ($this->fastExcelCallback ? $this->fastExcelCallback() : null)
            : $this->csvWriter;

        return $this->buildExcelFile()->download($this->getFilename() . $ext, $callback);
    }

    /**
     * Export results to PDF file.
     *
     * @return mixed
     */
    public function pdf()
    {
        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        return $this->buildExcelFile()->download($this->getFilename() . '.pdf', $this->pdfWriter);
    }

    /**
     * PDF version of the table using print preview blade template.
     *
     * @return mixed
     */
    public function snappyPdf()
    {
        /** @var \Barryvdh\Snappy\PdfWrapper $snappy */
        $snappy      = app('snappy.pdf.wrapper');
        $options     = config('datatables-buttons.snappy.options');
        $orientation = config('datatables-buttons.snappy.orientation');

        $snappy->setOptions($options)->setOrientation($orientation);

        return $snappy->loadHTML($this->printPreview())->download($this->getFilename() . '.pdf');
    }

    /**
     * Add basic array query scopes.
     *
     * @param  \Yajra\DataTables\Contracts\DataTableScope  $scope
     * @return $this
     */
    public function addScope(DataTableScope $scope)
    {
        $this->scopes[] = $scope;

        return $this;
    }

    /**
     * Push multiples scopes to array query scopes.
     *
     * @param  array  $scopes
     * @return $this
     */
    public function addScopes(array $scopes)
    {
        $this->scopes = array_merge($this->scopes, $scopes);

        return $this;
    }

    /**
     * Set a custom class attribute.
     *
     * @param  mixed  $key
     * @param  mixed|null  $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->attributes = array_merge($this->attributes, $key);
        } else {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param  string  $key
     * @return mixed|null
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    /**
     * Apply query scopes.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    protected function applyScopes($query)
    {
        foreach ($this->scopes as $scope) {
            $scope->apply($query);
        }

        return $query;
    }

    /**
     * Determine if the DataTable has scopes.
     *
     * @param  array  $scopes
     * @param  bool  $validateAll
     * @return bool
     */
    protected function hasScopes(array $scopes, $validateAll = false)
    {
        $filteredScopes = array_filter($this->scopes, function ($scope) use ($scopes) {
            return in_array(get_class($scope), $scopes);
        });

        return $validateAll ? count($filteredScopes) === count($scopes) : ! empty($filteredScopes);
    }

    /**
     * Get default builder parameters.
     *
     * @return array
     */
    protected function getBuilderParameters()
    {
        return config('datatables-buttons.parameters');
    }

    /**
     * @param  \Illuminate\Support\|array  $collection
     * @return \Illuminate\Support\Collection
     */
    protected function convertToLazyCollection($collection)
    {
        if (is_array($collection)) {
            $collection = collect($collection);
        }

        if (method_exists($collection, 'lazy')) {
            $collection->lazy();
        }

        return $collection;
    }

    /**
     * @return \Closure
     */
    public function fastExcelCallback()
    {
        return function ($row) {
            $mapped = [];
            foreach ($this->exportColumns() as $column) {
                if ($column['exportable']) {
                    $mapped[$column['title']] = $row[$column['name']];
                }
            }

            return $mapped;
        };
    }

    /**
     * @return \Rap2hpoutre\FastExcel\FastExcel
     */
    protected function buildFastExcelFile()
    {
        $query = null;
        if (method_exists($this, 'query')) {
            $query = app()->call([$this, 'query']);
            $query = $this->applyScopes($query);
        }

        /** @var \Yajra\DataTables\DataTableAbstract $dataTable */
        $dataTable = app()->call([$this, 'dataTable'], compact('query'));
        $dataTable->skipPaging();

        if ($dataTable instanceof QueryDataTable) {
            function queryGenerator($dataTable)
            {
                foreach ($dataTable->getFilteredQuery()->cursor() as $row) {
                    yield $row;
                }
            }

            return new FastExcel(queryGenerator($dataTable));
        }

        return new FastExcel($this->convertToLazyCollection($dataTable->toArray()['data']));
    }
}
