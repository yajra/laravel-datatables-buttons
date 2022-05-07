<?php

namespace Yajra\DataTables\Buttons\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    /** @test */
    public function it_returns_view_on_normal_get_request()
    {
        $response = $this->get('users');

        $response->assertSeeText('users-table');
        $response->assertSeeText('LaravelDataTables');
    }

    /** @test */
    public function it_can_return_a_csv_file()
    {
        $response = $this->get('users?action=csv');

        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
    }

    /** @test */
    public function it_can_return_a_xls_file()
    {
        $response = $this->get('users?action=excel');

        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
    }

    /** @test */
    public function it_can_return_a_pdf_file()
    {
        $response = $this->get('users?action=pdf');

        $this->assertInstanceOf(Response::class, $response->baseResponse);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->get('/users', function (UsersDataTable $dataTable) {
            return $dataTable->render('tests::users');
        });
    }
}
