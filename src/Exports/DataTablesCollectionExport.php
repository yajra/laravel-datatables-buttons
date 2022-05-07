<?php

namespace Yajra\DataTables\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

abstract class DataTablesCollectionExport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @var Collection
     */
    protected Collection $collection;

    /**
     * @param  \Illuminate\Support\Collection|null  $collection
     */
    public function __construct(Collection $collection = null)
    {
        $this->collection = $collection ?? new Collection;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
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
