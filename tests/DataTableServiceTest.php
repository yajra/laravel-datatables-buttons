<?php

namespace Yajra\DataTables\Buttons\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Buttons\Tests\DataTables\UsersDataTable;
use Yajra\DataTables\Buttons\Tests\Models\User;
use Yajra\DataTables\EloquentDataTable;

class DataTableServiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_handle_ajax_request(): void
    {
        $response = $this->getAjax('/users');

        $response->assertJson([
            'draw' => 0,
            'recordsTotal' => 20,
            'recordsFiltered' => 20,
        ]);
    }

    /** @test */
    public function it_returns_view_on_normal_get_request(): void
    {
        $response = $this->get('users');

        $response->assertSeeText('users-table');
        $response->assertSeeText('LaravelDataTables');
    }

    /** @test */
    public function it_can_return_a_csv_file(): void
    {
        $response = $this->get('users?action=csv');

        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
    }

    /** @test */
    public function it_can_return_a_xls_file(): void
    {
        $response = $this->get('users?action=excel');

        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
    }

    /** @test */
    public function it_can_return_a_pdf_file(): void
    {
        $response = $this->get('users?action=pdf');

        $this->assertInstanceOf(Response::class, $response->baseResponse);
    }

    /** @test */
    public function it_allows_before_response_callback(): void
    {
        $response = $this->getAjax('users/before');
        $response->assertOk();

        $row = $response['data'][0];
        $this->assertEquals($row['name'].'X', $row['nameX']);
    }

    /** @test */
    public function it_allows_response_callback(): void
    {
        $response = $this->getAjax('users/response');
        $response->assertOk();

        $this->assertEquals(2, $response->json('recordsTotal'));
        $this->assertEquals(1, $response->json('recordsFiltered'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $router = $this->app['router'];
        $router->get('/users', function (UsersDataTable $dataTable) {
            return $dataTable->render('tests::users');
        });

        $router->get('/users/before', function (UsersDataTable $dataTable) {
            return $dataTable->before(function (EloquentDataTable $dataTable) {
                $dataTable->addColumn('nameX', fn (User $user) => $user->name.'X');
            })->render('tests::users');
        });

        $router->get('/users/response', function (UsersDataTable $dataTable) {
            return $dataTable->response(function (Collection $data) {
                $data['recordsTotal'] = 2;
                $data['recordsFiltered'] = 1;

                return $data;
            })->render('tests::users');
        });
    }
}
