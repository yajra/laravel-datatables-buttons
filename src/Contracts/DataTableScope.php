<?php

namespace Yajra\DataTables\Contracts;

interface DataTableScope
{
    /**
     * Apply a query scope.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Support\Collection|\Illuminate\Http\Resources\Json\AnonymousResourceCollection  $query
     * @return mixed
     */
    public function apply($query);
}
