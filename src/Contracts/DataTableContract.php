<?php

namespace Yajra\Datatables\Contracts;

/**
 * Interface DataTableContract
 *
 * @package Yajra\Datatables\Contracts
 * @author  Arjay Angeles <aqangeles@gmail.com>
 */
interface DataTableContract
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax();

    /**
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query();
}
