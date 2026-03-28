<?php

namespace Yajra\DataTables\Buttons\Tests\DataTables;

use Yajra\DataTables\Html\Column;

class UsersDataTableOpenSpoutExportOptions extends UsersDataTableOpenSpout
{
    public function html(): \Yajra\DataTables\Html\Builder
    {
        return $this->builder()
            ->setTableId('users-openspout-export-options')
            ->minifiedAjax()
            ->columns([
                Column::make('id')->title('ID')->exportFormat('0.00'),
                Column::make('name')->exportRender(fn ($row, $data) => '['.$data.']'),
            ]);
    }
}
