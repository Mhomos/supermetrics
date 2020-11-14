<?php
namespace App\Controllers;

use App\Services\MetricReportService;
use Symfony\Component\HttpFoundation\Response;

class MetricReportController
{
    /**
     * @var MetricReportService
     */
    private $metricReportService;

    /**
     * MetricReportController constructor.
     *
     */
    public function __construct()
    {
        $this->metricReportService = new MetricReportService();
    }

    /**
     * Show a Report data.
     *
     * @param $report
     * @return Response
     */
    public function show($report): Response
    {
        $report = $this->metricReportService->setReport($report)->validateReport()->generateReport();

        return \Framework\Core::jsonSuccessFormat($report->getResult());
    }
}
