<?php

namespace Yajra\DataTables\Buttons\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Yajra\DataTables\Buttons\Tests\DataTables\UsersDataTable;

class DataTableServiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_handle_ajax_request()
    {
        $response = $this->getAjax('/users');

        $response->assertJson([
            'draw' => 0,
            'recordsTotal' => 20,
            'recordsFiltered' => 20,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->get('/users', function (UsersDataTable $dataTable) {
            return $dataTable->render('tests::users');
        });
    }
}
