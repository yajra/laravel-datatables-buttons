<?php

namespace Yajra\DataTables\Buttons\Tests\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Buttons\Tests\Models\User;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function dataTable(Builder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id');
    }

    public function query(User $user): Builder
    {
        return $user->newQuery()->select('*');
    }

    public function html(): \Yajra\DataTables\Html\Builder
    {
        return $this->builder()
                    ->setTableId('users-table')
                    ->minifiedAjax()
                    ->columns([
                        Column::make('id'),
                        Column::make('name'),
                    ]);
    }

    protected function filename(): string
    {
        return 'Users';
    }
}
