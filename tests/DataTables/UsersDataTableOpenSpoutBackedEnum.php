<?php

namespace Yajra\DataTables\Buttons\Tests\DataTables;

use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

class UsersDataTableOpenSpoutBackedEnum extends UsersDataTableOpenSpout
{
    public function html(): Builder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->minifiedAjax()
            ->columns([
                Column::make('id'),
                Column::make('name'),
                Column::make('status'),
            ]);
    }
}
