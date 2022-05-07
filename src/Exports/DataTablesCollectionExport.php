<?php

namespace Yajra\DataTables\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

abstract class DataTablesCollectionExport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @var Collection|LazyCollection
     */
    protected $collection;

    /**
     * @param  Collection|LazyCollection|null  $collection
     */
    public function __construct($collection = null)
    {
        $this->collection = $collection ?? new Collection;
    }

    /**
     * @return Collection|LazyCollection
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        /** @var array $first */
        $first = $this->collection->first();
        if ($first) {
            return array_keys($first);
        }

        return [];
    }
}
