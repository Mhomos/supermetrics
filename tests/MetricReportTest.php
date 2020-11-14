<?php

namespace Tests;


use App\Helpers\HelperMethods;
use PHPUnit\Framework\TestCase;

class  MetricReportTest extends TestCase
{
    /**
     * @var mixed
     */
    private $appUrl;

    public function testAllReports()
    {
        $this->appUrl = HelperMethods::config('app_url');

        include_once './app/Helpers/HelperMethods.php';

        $reports = HelperMethods::config('reports');

        foreach ($reports as $report) {
            fwrite(STDERR, sprintf("Testing %s Report ... ", $report['description']));
            $this->isUrlWorking($report);
            fwrite(STDERR, "Working..." . PHP_EOL);
        }

        $this->assertTrue(true, "Reports are working.");
    }


    public function isUrlWorking($report)
    {
        $url = sprintf("%smetric/%s", $this->appUrl, $report['key']);

        $data = file_get_contents($url);
        $result = json_decode($data, true);
        $this->assertEquals(200, $result['status']);
        $this->assertNotEmpty($result['data']);
    }
}
