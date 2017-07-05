<?php

namespace Yajra\DataTables\Contracts;

interface DataTableService
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax();

    /**
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query();
}
