<?php

namespace Yajra\DataTables\Buttons\Tests;

use Illuminate\Routing\Router;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Buttons\Tests\DataTables\UsersDataTableOpenSpout;
use Yajra\DataTables\Buttons\Tests\DataTables\UsersDataTableOpenSpoutExportOptions;
use ZipArchive;

class OpenSpoutExportTest extends TestCase
{
    #[Test]
    public function it_streams_xlsx_when_fast_export_is_enabled(): void
    {
        $response = $this->get('/users-openspout?action=excel');

        $response->assertOk();
        $this->assertInstanceOf(StreamedResponse::class, $response->baseResponse);
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $response->headers->get('Content-Type')
        );
    }

    #[Test]
    public function it_streams_csv_when_fast_export_is_enabled(): void
    {
        $response = $this->get('/users-openspout?action=csv');

        $response->assertOk();
        $this->assertInstanceOf(StreamedResponse::class, $response->baseResponse);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
    }

    #[Test]
    public function it_applies_export_render_on_openspout_csv_export(): void
    {
        $response = $this->get('/users-openspout-export-options?action=csv');

        $response->assertOk();
        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $response->streamedContent());
        $this->assertStringContainsString('ID', $csv);
        $this->assertStringContainsString('Name', $csv);
        $this->assertStringContainsString('[Record-1]', $csv);
        $this->assertStringNotContainsString(',Record-1', $csv);
    }

    #[Test]
    public function it_applies_export_format_on_openspout_xlsx_export(): void
    {
        $response = $this->get('/users-openspout-export-options?action=excel');

        $response->assertOk();
        $binary = $response->streamedContent();
        $this->assertStringStartsWith('PK', $binary, 'XLSX should be a ZIP archive');

        $tmp = tempnam(sys_get_temp_dir(), 'dt-openspout');
        $this->assertNotFalse($tmp);
        try {
            file_put_contents($tmp, $binary);
            $zip = new ZipArchive;
            $this->assertTrue($zip->open($tmp), 'Should open generated XLSX as ZIP');
            try {
                $styles = $zip->getFromName('xl/styles.xml');
                $this->assertNotFalse($styles);
                // OpenSpout maps "0.00" to Excel built-in number format id 2 (see ECMA-376 numFmtId).
                $this->assertStringContainsString('numFmtId="2"', (string) $styles, 'exportFormat should register a decimal number style');
            } finally {
                $zip->close();
            }
        } finally {
            if (is_string($tmp) && file_exists($tmp)) {
                unlink($tmp);
            }
        }
    }

    #[Test]
    public function it_applies_export_render_on_openspout_xlsx_export(): void
    {
        $response = $this->get('/users-openspout-export-options?action=excel');

        $response->assertOk();
        $tmp = tempnam(sys_get_temp_dir(), 'dt-openspout-read');
        $this->assertNotFalse($tmp);
        try {
            file_put_contents($tmp, $response->streamedContent());
            $reader = new XlsxReader;
            $reader->open($tmp);
            try {
                $rows = [];
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        $rows[] = $row->toArray();
                    }
                }
                $this->assertGreaterThanOrEqual(2, count($rows));
                $this->assertSame(['ID', 'Name'], $rows[0]);
                $this->assertSame(1, $rows[1][0]);
                $this->assertSame('[Record-1]', $rows[1][1]);
            } finally {
                $reader->close();
            }
        } finally {
            if (is_string($tmp) && file_exists($tmp)) {
                unlink($tmp);
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Router $router */
        $router = $this->app['router'];
        $router->get('/users-openspout', fn (UsersDataTableOpenSpout $dataTable) => $dataTable->render('tests::users'));
        $router->get('/users-openspout-export-options', fn (UsersDataTableOpenSpoutExportOptions $dataTable) => $dataTable->render('tests::users'));
    }
}
