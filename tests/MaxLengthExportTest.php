<?php

namespace Yajra\DataTables\Buttons\Tests;

use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Yajra\DataTables\Buttons\Tests\DataTables\UsersDataTable;

class MaxLengthExportTest extends TestCase
{
    #[Test]
    public function it_exports_every_record_when_a_max_length_is_configured(): void
    {
        config(['datatables.max_length' => 2]);

        $this->assertCount(20, $this->ajaxResponseData());
    }

    #[Test]
    public function it_exports_every_record_without_a_max_length(): void
    {
        $this->assertCount(20, $this->ajaxResponseData());
    }

    protected function ajaxResponseData(): array
    {
        $dataTable = app(UsersDataTable::class);

        return (new ReflectionMethod($dataTable, 'getAjaxResponseData'))->invoke($dataTable);
    }
}
