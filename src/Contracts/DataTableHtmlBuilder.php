<?php

namespace Yajra\DataTables\Contracts;

use Yajra\DataTables\Html\Builder;

interface DataTableHtmlBuilder
{
    /**
     * Handle building of dataTables html.
     *
     * @return \Yajra\DataTables\Html\Builder
     * @throws \Exception
     */
    public function handle();
}
