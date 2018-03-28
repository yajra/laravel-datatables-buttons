<?php

namespace Yajra\DataTables\Services;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class DataTablesExportHandler implements FromCollection
{
    use Exportable;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * DataTablesExportHandler constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->collection;
    }
}
